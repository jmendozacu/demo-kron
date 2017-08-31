<?php

class Ebizmarts_BakerlooRestful_Helper_Sales extends Mage_Core_Helper_Abstract {

    private $_quote = null;

    /**
     * Build quote for order.
     *
     * @param $storeId
     * @param $data
     * @param bool $onlyQuote
     * @return mixed
     */
    public function buildQuote($storeId, $data, $onlyQuote = false) {

        Mage::app()->getStore()->setCurrentCurrencyCode($data->currency_code);

        $quote = Mage::getModel('sales/quote')
            ->setStoreId($storeId)
            ->setIsMultiShipping(false);

        $this->setQuote($quote);

        //Adding products to Quote
        $this->_addProductsToQuote($data->products);

        if(!$this->getQuote()->isVirtual()) {
            $shippingAddress = $this->_getAddress($data->customer->shipping_address, $data->customer->email);

            $this->getQuote()->getShippingAddress()
                ->addData($shippingAddress)
                ->setCollectShippingRates(true);
        }

        if($onlyQuote) {

            if ($this->getQuote()->isVirtual()) {
                $this->getQuote()->getBillingAddress()->getTotals();
            }
            else {
                $this->getQuote()->getShippingAddress()->collectTotals();
            }

            return $this->getQuote();
        }

        $customerExists = $this->customerExists($data->customer->email, Mage::app()->getStore()->getWebsiteId());

        $customerId = (int)$data->customer->customer_id;
        if(false !== $customerExists) {
            $customerId = $customerExists->getId();
        }

        if(Mage_Checkout_Model_Type_Onepage::METHOD_GUEST == $data->customer->mode && (false === $customerExists)) {

            $ownerEmail = (string)Mage::app()->getStore()->getConfig('trans_email/ident_general/email');

            if( (((string)$data->customer->email) != $ownerEmail) and (1 === (int)Mage::helper('bakerloo_restful')->config('checkout/create_customer')) ) {
                //Involve new customer if the one provided does not exist
                $this->_involveNewCustomer($data);
            }
            else {

                $this->getQuote()->setCheckoutMethod($data->customer->mode);

                $this->getQuote()
                    ->setCustomerEmail($data->customer->email)
                    ->setCustomerId(null)
                    ->setCustomerIsGuest(true)
                    ->setCustomerFirstname($data->customer->firstname)
                    ->setCustomerLastname($data->customer->lastname);

                $this->getQuote()->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
            }

        }
        else {
            $customer = Mage::getModel("customer/customer")->load($customerId);
            $this->getQuote()
                ->setCustomer($customer)
                ->setPasswordHash($customer->encryptPassword($customer->getPassword()));

            /*Fix for TBT_Rewards, points not saved to customer otherwise.*/
            $this->getQuote()->save();
            /*Fix for TBT_Rewards, points not saved to customer otherwise.*/
        }

        if($data->total_amount == 0 && !$this->getQuote()->isVirtual()) {

            $this->getQuote()->getShippingAddress()
                ->setShippingMethod($data->shipping);
        }
        else {

            if($this->getQuote()->isVirtual()) {
                $this->getQuote()->getBillingAddress()
                    ->setPaymentMethod($data->payment->method);
            }
            else {
                $this->getQuote()->getShippingAddress()
                    ->setPaymentMethod($data->payment->method)
                    ->setShippingMethod($data->shipping);
            }

        }

        $billingAddress  = $this->_getAddress($data->customer->billing_address, $data->customer->email);
        $this->getQuote()->getBillingAddress()
            ->addData($billingAddress);

        //Apply coupon if present
        $checkCouponOK = false;
        if(isset($data->coupon_code) && !empty($data->coupon_code)) {
            $couponCode = $data->coupon_code;

            $this->getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '');

            $checkCouponOK = true;
        }

        //Apply gift cards if present
        $giftCards = isset($data->gift_card) ? $data->gift_card : null;
        if(!empty($giftCards) and is_array($giftCards)) {

            foreach($giftCards as $_giftCardCode) {
                Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
                    ->loadByCode($_giftCardCode)
                    ->addToCart(false, $this->getQuote());
            }

        }

        if ($this->getQuote()->isVirtual()) {
            $this->getQuote()->getBillingAddress()->getTotals();
        }
        else {
            $this->getQuote()->getShippingAddress()->collectTotals();
        }

        if($data->total_amount != 0) {

            //Use Reward Points
            if(isset($data->payment->use_reward_points) and ((int)$data->payment->use_reward_points === 1)) {
                $this->getQuote()->setUseRewardPoints(true);
            }

            //Use Customer Balance
            if(isset($data->payment->use_customer_balance) and ((int)$data->payment->use_customer_balance === 1)) {
                $this->getQuote()->setUseCustomerBalance(true);
            }

            $this->getQuote()->getPayment()->importData((array)$data->payment);
        }
        else {
            $noPayment = array('method' => 'bakerloo_free');
            $this->getQuote()->getPayment()->importData($noPayment);
        }

        $this->getQuote()->collectTotals()->save();

        //If coupon was provided and does not validate, throw error.
        if($checkCouponOK) {
            if (!$this->getQuote()->getCouponCode()) {
                Mage::throwException( Mage::helper('bakerloo_restful')->__('Discount coupon could not be applied, please try again.') );
            }
        }

        return $this->getQuote();
    }


    private function _addProductsToQuote($products) {

        foreach($products as $_product) {
            $product = Mage::getModel('catalog/product')->load((int)$_product->product_id);

            if(!$product->getId()) {
                Mage::throwException('Product ID: ' . $_product->product_id . " does not exist.");
            }

            $buyInfo = array('qty' => ($_product->qty * 1));

            //Configurable attributes
            if(isset($_product->super_attribute)) {

                $superAttribute = (array)$_product->super_attribute;
                if(is_array($superAttribute) && !empty($superAttribute)) {

                    $superRequest = array();

                    foreach($superAttribute as $_at) {

                        $attribute = Mage::getModel('catalog/resource_eav_attribute')
                            ->loadByCode(Mage_Catalog_Model_Product::ENTITY, (string)$_at->attribute_code);

                        $superRequest[$attribute->getId()] = (string)$_at->value_index;

                    }

                    $buyInfo['super_attribute'] = $superRequest;
                }

            }

            //Bundle product
            if(isset($_product->bundle_option)) {

                $buyInfo['product']         = $_product->product_id;
                $buyInfo['related_product'] = '';

                $buyInfo['bundle_option']     = array();
                $buyInfo['bundle_option_qty'] = array();

                foreach($_product->bundle_option as $bundle) {

                    $optionType = $bundle->type;
                    $optionId   = (int)$bundle->id;
                    $selections = $bundle->selections;

                    $chosen = array();

                    if(is_array($selections) and !empty($selections)) {
                        foreach($selections as $_sel) {

                            if(isset($_sel->selected)) {
                                if(1 === ((int)$_sel->selected)) {

                                    $selectedId = (int)$_sel->id;

                                    if($optionType == 'multi' or $optionType == 'checkbox') {
                                        $buyInfo['bundle_option'][$optionId][] = $selectedId;
                                    }
                                    else {
                                        if($optionType == 'radio' or $optionType == 'select') {
                                            $buyInfo['bundle_option'][$optionId] = $selectedId;
                                        }
                                    }

                                    $buyInfo['bundle_option_qty'][$optionId] = ($_sel->qty * 1);

                                }
                            }
                        }
                    }

                }

            }

            //Product custom options
            if(isset($_product->options)) {

                $options = (array)$_product->options;

                $optionsRequest = array();

                foreach($options as $_opt) {
                    $selected = (int)$_opt->option_type_id;

                    if($selected) {
                        $optionsRequest[$_opt->option_id] = $selected;
                    }
                    else {
                        if(isset($_opt->text)) {
                            $optionsRequest[$_opt->option_id] = (string)$_opt->text;
                        }
                    }
                }

                $buyInfo['options'] = $optionsRequest;
            }

            if((string)$_product->type == 'giftcard') {
                if(isset($_product->gift_card_options)) {

                    $giftCardData = $_product->gift_card_options;

                    $amount = $giftCardData->amount;

                    $amounts = $giftCardData->amounts;

                    $customAmount = true;

                    if(!empty($amounts)) {
                        for ($i = 0; $i < count($amounts); $i++) {
                            if( ($amount == $amounts[$i]->value)
                            or ($amount == $amounts[$i]->website_value) ) {
                                $customAmount = false;
                            }
                        }
                    }

                    $buyInfo['custom_giftcard_amount']   = ($customAmount ? $giftCardData->amount : '');
                    $buyInfo['giftcard_amount']          = ($customAmount ? '' : $giftCardData->amount);
                    $buyInfo['giftcard_sender_name']     = $giftCardData->sender_name;
                    $buyInfo['giftcard_sender_email']    = $giftCardData->sender_email;
                    $buyInfo['giftcard_recipient_name']  = $giftCardData->recipient_name;
                    $buyInfo['giftcard_recipient_email'] = $giftCardData->recipient_email;
                    $buyInfo['giftcard_message']         = $giftCardData->comments;

                }
            }

            try {

                if( ((int)Mage::helper('bakerloo_restful')->config('catalog/allow_backorders')) ) {

                    if( !Mage::registry(Ebizmarts_BakerlooRestful_Model_Rewrite_CatalogInventory_Stock_Item::BACKORDERS_YES) )
                        Mage::register(Ebizmarts_BakerlooRestful_Model_Rewrite_CatalogInventory_Stock_Item::BACKORDERS_YES, true);
                }

                $quoteItem = $this->getQuote()->addProduct($product, new Varien_Object($buyInfo));

            }catch (Exception $qex) {
                Mage::throwException("An error occurred, Product SKU: {$product->getSku()}. Error Message: {$qex->getMessage()}");
            }

            if(is_string($quoteItem)) {
                Mage::throwException($quoteItem . ' Product ID: ' . $_product->product_id);
            }

            //@TODO: Discount amount per line, see discount.
            if( property_exists($_product, 'is_custom_price') ) {

                if( (int)$_product->is_custom_price === 1 )
                    $this->_applyCustomPrice($quoteItem, $_product->price);

            }
            elseif(isset($_product->price)) {

                $this->_applyCustomPrice($quoteItem, $_product->price);

            }

            //Discount reasons
            if(isset($_product->discount_reason)) {
                if($quoteItem->getParentItem()) {
                    $quoteItem->getParentItem()->setPosDiscountReason($_product->discount_reason);
                }
                else {
                    $quoteItem->setPosDiscountReason($_product->discount_reason);
                }
            }

            unset($product);
        }

    }

    private function _applyCustomPrice($quoteItem, $price) {
        if($quoteItem->getParentItem()) {
            $quoteItem->getParentItem()->setCustomPrice($price);
            $quoteItem->getParentItem()->setOriginalCustomPrice($price);
        }
        else {
            $quoteItem->setCustomPrice($price);
            $quoteItem->setOriginalCustomPrice($price);
        }
    }

    private function _getCustomerAddress($addressId) {
        $address = Mage::getModel('customer/address')->load((int)$addressId);
        if (is_null($address->getId())) {
            return null;
        }

        $address->explodeStreetAddress();
        if ($address->getRegionId()) {
            $address->setRegion($address->getRegionId());
        }
        return $address;
    }

    private function _getAddress($data, $email = "") {

        $address = array(
            'firstname'  => $data->firstname,
            'lastname'   => $data->lastname,
            'email'      =>  $email,
            'is_active'  => 1,
            'street'     => $data->street,
            'street1'    => $data->street,
            'city'       => $data->city,
            'region_id'  => $data->region_id,
            'region'     => $data->region,
            'postcode'   => $data->postcode,
            'country_id' => $data->country_id,
            'telephone'  =>  $data->telephone,
        );

        $id = (int)$data->customer_address_id;
        if($id) {
            $_address = $this->_getCustomerAddress($id);
            if($_address) {
                $address = $_address->getData();
            }
        }

        return $address;
    }

    /**
     * Create new customer if the one provided does not exist.
     *
     * @param  string $data JSON data
     * @return void
     */
    private function _involveNewCustomer($data) {

        $email = (string)$data->customer->email;

        $this->getQuote()->setCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER);

        /* @see Mage_Checkout_Model_Type_Onepage::_validateCustomerData */
        /* @var $customerForm Mage_Customer_Model_Form */

        $websiteId = Mage::app()->getStore()->getWebsiteId();
        $customer  = $this->customerExists($email, $websiteId);

        if(false === $customer) {

            $password     = substr(uniqid(), 0, 8);
            $customer     = Mage::helper('bakerloo_restful')->createCustomer($websiteId, $data, $password);
            $passwordHash = $customer->hashPassword($password);

            $addAddress = true;

            //Billing Address
            $address  = $this->_getAddress($data->customer->billing_address, $data->customer->email);

            //Check that the address provided is not the store's, if thats the case, ignore it.
            $storeAddress = Mage::helper('bakerloo_restful')->getStoreAddress(Mage::app()->getStore()->getId());
            if(is_array($storeAddress) and !empty($storeAddress)) {
                $eqPostcode  = ($storeAddress['postal_code'] == $address['postcode']);
                $eqCountry   = ($storeAddress['country'] == $address['country_id']);
                $eqTelephone = ($storeAddress['telephone'] == $address['telephone']);
                $eqRegion    = ($storeAddress['region_id'] == $address['region_id']);

                $addAddress = !($eqPostcode and $eqCountry and $eqTelephone and $eqRegion);
            }

            if($addAddress) {
                $newAddress = Mage::getModel('customer/address');
                $newAddress->addData($address);
                $newAddress->setId(null)
                    ->setIsDefaultBilling(true)
                    ->setIsDefaultShipping(true);
                $customer->addAddress($newAddress);

                $addressErrors = $newAddress->validate();
                if (is_array($addressErrors)) {
                    Mage::throwException(implode("\n", $addressErrors));
                }
            }

            //@TODO: Check this, Magento should save customer when checkout method is REGISTER
            //its not doing it so we call save() manually
            $customer->save();

            $this->getQuote()->setPasswordHash($passwordHash);
            $this->getQuote()->setCustomerGroupId($customer->getGroupId());
            $this->getQuote()->setCustomerIsGuest(false);

            // copy customer data to quote
            Mage::helper('core')->copyFieldset('customer_account', 'to_quote', $customer, $this->getQuote());

        }

    }

    /**
     * Check if customer email exists
     *
     * @param string $email
     * @param int $websiteId
     * @return false|Mage_Customer_Model_Customer
     */
    public function customerExists($email, $websiteId = null) {
        $customer = Mage::getModel('customer/customer');
        if ($websiteId != null) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);
        if ($customer->getId()) {
            return $customer;
        }
        return false;
    }

    public function getQuote() {
        return $this->_quote;
    }

    public function setQuote($aQuote) {
        $this->_quote = $aQuote;
    }

    /**
     * Put notification on admin panel.
     *
     * @param  array  $notification
     * @return void
     */
    public function notifyAdmin(array $notification) {
        if (!empty($notification)) {
            Mage::getModel('adminnotification/inbox')->parse(array($notification));
        }
    }

    public function getCartData($quote) {
        $cartData = array(
            'quote_currency_code'         => $quote->getQuoteCurrencyCode(),
            'grand_total'                 => $quote->getGrandTotal(),
            'base_grand_total'            => $quote->getBaseGrandTotal(),
            'sub_total'                   => $quote->getSubtotal(),
            'base_subtotal'               => $quote->getBaseSubtotal(),
            'subtotal_with_discount'      => $quote->getSubtotalWithDiscount(),
            'base_subtotal_with_discount' => $quote->getBaseSubtotalWithDiscount(),
            'items'                       => array(),
        );

        foreach($quote->getItemsCollection(false) as $quoteItem) {

            if ($quoteItem->getParentItem()) {
                continue;
            }

            $item = array(
                'sku'                     => $quoteItem->getSku(),
                'product_id'              => (int)$quoteItem->getProductId(),
                'qty'                     => ($quoteItem->getQty() * 1),
                'price'                   => $quoteItem->getPrice(),
                'price_incl_tax'          => $quoteItem->getPriceInclTax(),
                'base_price_incl_tax'     => (float)$quoteItem->getBasePriceInclTax(),
                'row_total'               => $quoteItem->getRowTotal(),
                'row_total_with_discount' => (float)$quoteItem->getRowTotalWithDiscount(),
                'row_total_incl_tax'      => $quoteItem->getRowTotalInclTax(),
                'base_row_total'          => $quoteItem->getBaseRowTotal(),
                'custom_price'            => (float)$quoteItem->getCustomPrice(),
                'discount_amount'         => $quoteItem->getDiscountAmount(),
                'tax_amount'              => (float)$quoteItem->getTaxAmount(),
            );

            $cartData['items'][] = $item;
        }

        return $cartData;
    }

    /**
     * Check if the customer associated to an order is guest.
     *
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    public function customerInOrderIsGuest(Mage_Sales_Model_Order $order) {
        return ((int)$order->getCustomerIsGuest() === 1);
    }

}