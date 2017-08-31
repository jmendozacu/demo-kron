<?php

class Ebizmarts_BakerlooRestful_Model_Api_Customers extends Ebizmarts_BakerlooRestful_Model_Api_Api {

    protected $_model = "customer/customer";

    public function checkPostPermissions() {
        //Validate permissions
        $this->checkPermission(array('bakerloo_api/login', 'bakerloo_api/customers/create'));
    }

    public function checkPutPermissions() {
        //Validate permissions
        $this->checkPermission(array('bakerloo_api/login', 'bakerloo_api/customers/update'));
    }

    protected function _getCollection() {

        Mage::app()->setCurrentStore($this->getStoreId());

        $collection = Mage::getResourceModel('customer/customer_collection')
                        ->addNameToSelect()
                        ->addAttributeToSelect('bakerloo_payment_methods');

        $shouldFilter = (int)Mage::helper("bakerloo_restful")->config("general/filter_customers", $this->getStoreId());

        if($shouldFilter === 1) {
            $collection->addAttributeToFilter(
                            array(
                                array('attribute'=> 'website_id','eq' => (int)Mage::app()->getStore()->getWebsiteId()),
                                array('attribute'=> 'website_id','eq' => 0), //Admin
                            )
                        );
        }

        return $collection;
    }

    public function _createDataObject($id = null, $data = null) {

        $result = array();

        if(is_null($data)) {
            $customer = Mage::getModel($this->_model)->load($id);
        }
        else {
            $customer = $data;
        }

        if($customer->getId()) {

            $websiteId = Mage::app()->getStore($this->getStoreId())->getWebsiteId();

            $websiteBaseCurrencyCode = Mage::app()->getWebsite($websiteId)->getBaseCurrencyCode();

            $result['customer_id']              = (int) $customer->getId();
            $result['firstname']                = $customer->getFirstname();
            $result['lastname']                 = $customer->getLastname();
            $result['email']                    = $customer->getEmail();
            $result['website_id']               = (int) $customer->getWebsiteId();
            $result['group_id']                 = (int) $customer->getGroupId();
            $result['bakerloo_payment_methods'] = (string)$customer->getBakerlooPaymentMethods();
            $result['subscribed_to_newsletter'] = (Mage::getModel('newsletter/subscriber')->loadByCustomer($customer)->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED);

            if("0000-00-00 00:00:00" == $customer->getUpdatedAt()) {
                $result['updated_at'] = Mage::getModel('core/date')->gmtDate();
            }
            else {
                $result['updated_at'] = $customer->getUpdatedAt();
            }

            //Lifetime sales value in base currency
            $result ['lifetime_sales'] = $this->getLifetimeSales($customer);

            //Customer Reward Points
            $rewardPointsAvailable = Mage::helper('bakerloo_restful')->isModuleInstalled('Enterprise_Reward');
            if(is_object($rewardPointsAvailable) and ((string)$rewardPointsAvailable->active) == 'true' and Mage::helper('enterprise_reward')->isEnabled()) {
                $reward = Mage::getModel('enterprise_reward/reward')
                ->getCollection()
                ->addFieldToFilter('customer_id', $result['customer_id'])
                ->addWebsiteFilter($websiteId)
                ->getFirstItem();

                $result['reward_points_balance']                = (int)$reward->getPointsBalance();
                $result['reward_points_amount']                 = $reward->getCurrencyAmount();
                $result['reward_points_website_currency_code']  = $websiteBaseCurrencyCode;
            }

            //Customer Store Credit
            $storeCreditAvailable = Mage::helper('bakerloo_restful')->isModuleInstalled('Enterprise_CustomerBalance');
            if($storeCreditAvailable and Mage::helper('enterprise_customerbalance')->isEnabled()) {
                $credit = Mage::getModel('enterprise_customerbalance/balance')
                ->setCustomerId($result['customer_id'])
                ->setWebsiteId($websiteId)
                ->loadByCustomer();

                $result['store_credit_amount']              = $credit->getAmount();
                $result['store_credit_base_currency_code']  = $websiteBaseCurrencyCode;
            }

            //Addresses
            $result['address'] = array();

            $addresses = $customer->getAddressesCollection();
            if($addresses->getSize()) {

                $defaultBillingId  = (int)$customer->getDefaultBilling();
                $defaultShippingId = (int)$customer->getDefaultShipping();

                foreach($addresses as $_address) {

                    $id = (int)$_address->getId();

                    $addr = array(
                                   "customer_address_id" => $id,
                                   "firstname"           => $_address->getFirstname(),
                                   "lastname"            => $_address->getLastname(),
                                   "country_id"          => $_address->getCountryId(),
                                   "city"                => $_address->getCity(),
                                   "street"              => $_address->getStreet(-1),
                                   "region_id"           => $_address->getRegionId(),
                                   "region"              => $_address->getRegion(),
                                   "postcode"            => $_address->getPostcode(),
                                   "telephone"           => $_address->getTelephone(),
                                   "fax"                 => $_address->getFax(),
                                   "company"             => $_address->getCompany(),
                                   "is_shipping_address" => ($defaultShippingId == $id) ? 1 : 0,
                                   "is_billing_address"  => ($defaultBillingId == $id) ? 1 : 0
                                  );

                    $result['address'] []= $addr;
                }

            }

            $result ['wishlist'] = $this->_getMyWishlist($customer->getId());

        }

        return $result;

    }


    protected function _getMyWishlist($customerId) {

        $wishlistItems = array();

        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);

        if ( !Mage::helper('wishlist')->isAllow() or !$wishlist->getId() or ((int)$wishlist->getCustomerId() != (int)$customerId) ) {
            return $wishlistItems;
        }

        if($wishlist->getItemsCount()) {
            $collection = $wishlist->getItemCollection();
            $collection->setInStockFilter(true)->setOrder('added_at', 'ASC');



            foreach($collection as $_item) {

                $_itemWishlist = array(
                    'added_at'    => $_item->getAddedAt(),
                    'description' => $_item->getDescription(),
                    'product_id'  => (int)$_item->getProductId(),
                    'qty'         => ($_item->getQty() * 1),
                    'store_id'    => (int)$_item->getStoreId(),
                );

                array_push($wishlistItems, $_itemWishlist);

            }

        }

        return $wishlistItems;

    }


    /**
     * Returns formatted lifetime sales value, eg: "£2,417.34"
     *
     * @param Mage_Customer_Model_Customer $customer
     *
     * @return array
     */
    public function getLifetimeSales(Mage_Customer_Model_Customer $customer) {
        $sales = Mage::getResourceModel('sales/sale_collection')
            ->setCustomerFilter($customer)
            ->setOrderStateFilter(Mage_Sales_Model_Order::STATE_CANCELED, true)
            ->load();

        $baseCurrencyCode = Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE);
        $baseCurrency = Mage::getModel('directory/currency')
                        ->load($baseCurrencyCode);

        $baseAmount = $sales->getTotals()->getBaseLifetime();

        return array(
                        'currency'         => $baseCurrencyCode,
                        'amount'           => $baseAmount,
                        'formatted_amount' => $baseCurrency->format($baseAmount, array(), false),
        );
    }

    /**
     * Create customer with addresses in Magento.
     *
     * @return $this|array
     */
    public function post() {

        parent::post();

        if(!$this->getStoreId()) {
            Mage::throwException('Please provide a Store ID.');
        }

        Mage::app()->setCurrentStore($this->getStoreId());

        $data = $this->getJsonPayload();

        $_customer = $data->customer;
        $email     = (string)$_customer->email;

        $websiteId       = Mage::app()->getStore()->getWebsiteId();
        $customerExists  = Mage::helper('bakerloo_restful/sales')->customerExists($email, $websiteId);

        if($customerExists === false) {

            $password     = substr(uniqid(), 0, 8);
            $customer     = $this->helper('bakerloo_restful')->createCustomer($websiteId, $data, $password);

            if(isset($_customer->address) && is_array($_customer->address) && !empty($_customer->address)) {

                foreach($_customer->address as $address) {
                    $address = array(
                        'firstname'  => $address->firstname,
                        'lastname'   => $address->lastname,
                        'email'      =>  $email,
                        'is_active'  => 1,
                        'street'     => $address->street,
                        'street1'    => $address->street,
                        'city'       => $address->city,
                        'region_id'  => $address->region_id,
                        'region'     => $address->region,
                        'postcode'   => $address->postcode,
                        'country_id' => $address->country_id,
                        'telephone'  =>  $address->telephone,
                    );

                    $newAddress = Mage::getModel('customer/address');
                    $newAddress->addData($address);
                    $newAddress->setId(null)
                        ->setIsDefaultBilling(true)
                        ->setIsDefaultShipping(true);
                    $customer->addAddress($newAddress);
                }
            }

            $customer->save();
        }
        else {
            Mage::throwException(Mage::helper('bakerloo_restful')->__("Customer already exists."));
        }

        return array('id'       => (int)$customer->getId(),
                     'email'    => $customer->getEmail(),
                     'store_id' => (int)$customer->getStoreId());
    }

    /**
     * Process customer update.
     *
     * @return $this|array
     */
    public function put() {

        parent::put();

        if(!$this->getStoreId()) {
            Mage::throwException('Please provide a Store ID.');
        }

        Mage::app()->setCurrentStore($this->getStoreId());

        $data = $this->getJsonPayload();

        $_customer = $data->customer;
        $email     = (string)$_customer->email;

        $websiteId = isset($_customer->website_id) ? (string)$_customer->website_id : Mage::app()->getStore()->getWebsiteId();
        $customer  = Mage::helper('bakerloo_restful/sales')->customerExists($email, $websiteId);

        if($customer !== false) {

            if(isset($_customer->address) && is_array($_customer->address) && !empty($_customer->address)) {

                foreach($_customer->address as $address) {

                    $addressId = isset($address->customer_address_id) ? (int)$address->customer_address_id : 0;

                    $_address = array(
                        'firstname'  => $address->firstname,
                        'lastname'   => $address->lastname,
                        'email'      => $email,
                        'is_active'  => 1,
                        'street'     => $address->street,
                        'street1'    => $address->street,
                        'city'       => $address->city,
                        'region_id'  => $address->region_id,
                        'region'     => $address->region,
                        'postcode'   => $address->postcode,
                        'country_id' => $address->country_id,
                        'telephone'  => $address->telephone,
                    );

                    if(isset($address->company)) {
                        $_address['company'] = $address->company;
                    }

                    $dbAddress = Mage::getModel('customer/address')->load($addressId);
                    $dbAddress->addData($_address);

                    //Add addresses
                    if($addressId === 0) {

                        $dbAddress->setId(null);
                        $dbAddress
                        ->setIsDefaultBilling(false)
                        ->setIsDefaultShipping(false);
                        $customer->addAddress($dbAddress);

                    }
                    else {
                        //Edit existing address
                        if($dbAddress->getId()) {
                            $dbAddress->save();
                        }

                    }
                }
            }

            //Edit Firstname and Lastname
            if(isset($_customer->firstname) and !empty($_customer->firstname)) {
                $customer->setFirstname($_customer->firstname);
            }

            if(isset($_customer->lastname) and !empty($_customer->lastname)) {
                $customer->setLastname($_customer->lastname);
            }

            $customer->save();

        }
        else {
            Mage::throwException(Mage::helper('bakerloo_restful')->__("Customer does not exist."));
        }

        return $this->_createDataObject($customer->getId());

    }

    /**
     * Retrieve DELETED customers.
     *
     * @return Collection data.
     */
    public function trashed() {
        $this->checkGetPermissions();

        $trash = Mage::getModel('bakerloo_restful/customertrash')
            ->getCollection();

        $since = $this->_getQueryParameter('since');
        if(!is_null($since)) {
            $trash->addFieldToFilter("updated_at", array("gt" => $since));
        }

        $items = $trash->getData();

        return $this->_getCollectionPageObject($items, 1, null, null, count($items));
    }

}