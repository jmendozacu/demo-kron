<?php
class MW_SocialGift_Model_Observer {

    public function checkLicense($o)
    {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;
        $modules2 = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        if (!in_array('MW_Mcore', $modules2) || !$modulesArray['MW_Mcore']->is('active') || Mage::getStoreConfig('mcore/config/enabled') != 1) {
            Mage::helper('mw_socialgift')->disableConfig();
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     * @var item
     */
    public function applyDiscount(Varien_Event_Observer $observer)
    {
        $this->checkEmptyCart(); 
        $params = Mage::app()->getRequest()->getParams() ;
        $socialgift = (isset($params['socialgift']) ? $params['socialgift'] : 0);
        $rule_id = (isset($params['rule']) ? $params['rule'] : 0);

        /* check product id in social gift list */
        $session = Mage::getSingleton('checkout/session');
        $SocialGiftIds = ($session->getSocialGiftIds() ? $session->getSocialGiftIds() : array());
        $item = $observer->getQuoteItem();
        $product_add_id = $item->getProduct()->getId();

        // check to add to cart this product social gift.
        if (($socialgift == 1) && in_array($product_add_id, $SocialGiftIds) && ($rule_id > 0)) {

            $ruleData = Mage::helper('mw_socialgift')->getRuleDataById($rule_id);

            // limit product with rule:
            if($session->getNumberSocialGiftRule()){
                $session->setNumberSocialGiftRule($ruleData['number_of_free_gift']);
            }

            $number_social_gift = $session->getNumberSocialGift();

            if (isset($number_social_gift) && ($number_social_gift >= 0) && ($number_social_gift < $ruleData['number_of_free_gift'])) {
                $can_add_to_cart = TRUE;
            }else{
                $can_add_to_cart = FALSE;
            }

            $totalItemsInCart = Mage::helper('checkout/cart')->getItemsCount();

            if( ($totalItemsInCart > 0) && ($can_add_to_cart === TRUE)) {
                $number_social_gift++;
                $session->setNumberSocialGift($number_social_gift);

                // Ensure we have the parent item, if it has one
                $item = ( $item->getParentItem() ? $item->getParentItem() : $item );

                // Load the custom price
                $ruleAmount = $ruleData['discount_amount'];
                $simple_action = $ruleData['simple_action'];

                $price = $this->_getPriceByItem($item->getProduct()->getPrice(), $ruleAmount, $simple_action);
                $qty = "1";

                // Set the custom price
                $item->setQty($qty);
                $item->setCustomPrice($price);
                $item->setOriginalCustomPrice($price);

                // add custom data option
                $SocialProductShared = $session->getSocialProductShared();
                $cart = Mage::getSingleton('checkout/cart');
                // $options = $item->getProduct()->getTypeInstance(TRUE)->getOrderOptions($item->getProduct());
                $infoRequest = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
                $infoRequest['option'] = serialize(array(
                        'label' =>  Mage::helper('mw_socialgift')->__('Social Sharing Gift'),
                        'value' => $ruleData['name'],
                        'shared_by' => end($SocialProductShared),
//                        'mw_socialgift_rule' => '1'
                    ));

                $_options = array(
                    array(
                        'label' => Mage::helper('mw_socialgift')->__('Social Sharing Gift'),
                        'value' => $ruleData['name'],
                        'print_value' => $ruleData['name'],
                        'option_type' => 'text',
                        'custom_view' => TRUE
                    )
                );

                $item->addOption(array(
                    'code' => 'additional_options',
                    'value' => serialize($_options),
                ));

                $item->getOptionByCode('info_buyRequest')->setValue(serialize($infoRequest));

                $observer->getProduct()->addCustomOption('additional_options', serialize($_options));
                $item->getProduct()->setIsSuperMode(TRUE);
                $item->getQuote()->save();
                $cart->save();
                $this->updateListGift($product_add_id, 'remove');
            }
        }
    }

    public function salesConvertQuoteItemToOrderItem(Varien_Event_Observer $observer)
    {
        $quoteItem = $observer->getItem();
        if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
            $orderItem = $observer->getOrderItem();
            $options = $orderItem->getProductOptions();
            $options['additional_options'] = unserialize($additionalOptions->getValue());
            $orderItem->setProductOptions($options);
        }
    }


    protected function _getPriceByItem($Price = 0, $ruleAmount = 1, $simple_action = 'by_percent')
    {
        $priceRule = 0;
        switch ($simple_action) {
            case 'to_fixed':
                $priceRule = min($ruleAmount, $Price);
                break;
            case 'to_percent':
                $priceRule = $Price * $ruleAmount / 100;
                break;
            case 'by_fixed':
                $priceRule = max(0, $Price - $ruleAmount);
                break;
            case 'by_percent':  
                $priceRule = $Price * (1 - $ruleAmount / 100);
                break;
        }
        return $priceRule;
    }

    public function checkCountryOfUser(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('checkout/session');
        $quote = $session->getQuote();

        $OriginSocialGiftIds =  ($session->getOriginSocialGiftIds() ? $session->getOriginSocialGiftIds() : array());
        $billing = Mage::app()->getRequest()->getPost('billing');
        if ($billing['country_id']) {
            $getAvailable = Mage::helper('mw_socialgift')->getAvailableSocialGiftByCountry($billing['country_id']);
            if ($getAvailable == 0)
            {
                // $this->removeSocialGiftInCart();
                $cartHelper = Mage::helper('checkout/cart');
                $items = $cartHelper->getCart()->getItems();       
                foreach ($items as $item) 
                {
                    $item_product_id = $item->getProduct()->getId();
                    if (in_array($item_product_id, $OriginSocialGiftIds))
                    {
                        $itemId = $item->getItemId();
                        $quote->removeItem($item->getId())->save();
                        $cartHelper->getCart()->removeItem($itemId)->save();
                    }
                }
                $this->resetGiftSession();
            }
        }
    }

    public function checkMergeItemGiftInCart(Varien_Event_Observer $observer)
    {
        if(!Mage::helper('mw_socialgift')->isEnabled()){
            return;
        }
        $customerQuote = Mage::getModel('sales/quote')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByCustomer(Mage::getSingleton('customer/session')->getCustomerId());
        $session               = Mage::getSingleton('checkout/session');
        $curren_quote_id       = $session->getQuoteId();
        $old_quote_id          = ($customerQuote->getId() ? $customerQuote->getId() : NULL );

        if ($old_quote_id != NULL && $curren_quote_id != $old_quote_id) {
            // Removing old cart items of the customer, only keep new cart items available.
            foreach ($customerQuote->getAllItems() as $item)
            {
                $infoRequest = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
                $countryId = Mage::helper('mw_socialgift')->_getCountry();
                $getAvailable = Mage::helper('mw_socialgift')->getAvailableSocialGiftByCountry($countryId);

                if(($getAvailable == 0) && isset($infoRequest['socialgift']) && ($infoRequest['socialgift'] == '1'))
                {
                    $social_product_id = $item->getProduct()->getId();
                    $item->isDeleted(TRUE);
                    if ($item->getHasChildren())
                    {
                        foreach ($item->getChildren() as $child)
                        {
                            $child->isDeleted(TRUE);
                        }
                    }
                }
            }

            /*$cartHelper = Mage::helper('checkout/cart');
            $cart_items =  $cartHelper->getCart()->getItems();

            foreach ($cart_items as $item)
            {
                $infoRequest = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
                $countryId = Mage::helper('mw_socialgift')->_getCountry();
                $getAvailable = Mage::helper('mw_socialgift')->getAvailableSocialGiftByCountry($countryId);
                mage::log($getAvailable);
                if(($getAvailable == 0) && isset($infoRequest['socialgift']) && ($infoRequest['socialgift'] == '1'))
                {
//                    Mage::log(get_class_methods($item));
//                    Mage::log($item->getProduct()->getPrice());
                    //$item->setCustomPrice($item->getProduct()->getPrice());
                    //$item->setPrice($item->getProduct()->getPrice());
//                    $social_product_id = $item->getProduct()->getId();
                    $item->isDeleted(TRUE);
                    if ($item->getHasChildren())
                    {
                        foreach ($item->getChildren() as $child)
                        {
                            $child->isDeleted(TRUE);
                        }
                    }
                }
            }*/
            $customerQuote->collectTotals()->save();
        }
    }

    /** 
    * Check do not allow update quantity product gift
    * Event checkout_cart_update_items_after
    * @var items
    */
    public function checkoutCartProductUpdateAfter(Varien_Event_Observer $observer) 
    {
        // Ensure cart is not empty.
        $session = Mage::getSingleton('checkout/session');
        $OriginSocialGiftIds = ($session->getOriginSocialGiftIds() ? $session->getOriginSocialGiftIds() : array());
        $items = $observer->getCart()->getQuote()->getAllVisibleItems();
        foreach ( $items as $item /* @var $item Mage_Sales_Model_Quote_Item */ ) {
            if( ($item->getQty() > 1) && (in_array($item->getProduct()->getId(), $OriginSocialGiftIds)) ) {
                $qty = "1";
                $item->setQty($qty);

                Mage::getSingleton('core/session')->addError(Mage::helper('mw_socialgift')->__('The quantity of Social Gift is limited. You can not update the quantity of gift greater than 1.')); 
                $item->getProduct()->setIsSuperMode(TRUE);
            }
        }
    }

    /**
     * Check product id was removed by user in cart, if it is a gift, add it to list gift again.
     * @var item
     */
    public function checkProductInSocialGift(Varien_Event_Observer $observer)
    {
        $infoRequest = array();
        $session = Mage::getSingleton('checkout/session');

        $OriginSocialGiftIds = ($session->getOriginSocialGiftIds() ? $session->getOriginSocialGiftIds() : array() );

        $quoteItem = $observer->getQuoteItem();
        $quoteItem = ( $quoteItem->getParentItem() ? $quoteItem->getParentItem() : $quoteItem );

        $product_id = $quoteItem->getProduct()->getId(); 
        $SocialProductShared = ( $session->getSocialProductShared() ? $session->getSocialProductShared() : array() ); 
        // remove product by parent share product
        if (in_array($product_id, $SocialProductShared)) 
        {
            $quote = $session->getQuote();
            $items = $quote->getAllVisibleItems();
            foreach ( $items as $item ) {
                $infoRequest = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
                if(isset($infoRequest['option'])) {
                    $option = unserialize($infoRequest['option']);
                    if($option['shared_by']){
                        $item_product_id = $item->getProduct()->getId();
                        if( in_array($item_product_id, $OriginSocialGiftIds) && ($option['shared_by'] == $product_id) ){
                            // remove out of quote
                            $quote->removeItem($item->getId())->save();
                            // update add list gift
                            $this->updateListGift($item_product_id, 'add');
                        }
                    }
                }
            }
            $this->resetGiftSession(); // <=> $session->setSocialGiftStatus('note_share');
        }

        if (in_array($product_id, $OriginSocialGiftIds)) {
            $this->updateListGift($product_id, 'add');
        }
        return;
    }

    protected function updateListGift($product_id, $action)
    {
        $session = Mage::getSingleton('checkout/session');
        $SocialGiftIds = ($session->getSocialGiftIds() ? $session->getSocialGiftIds() : array());
        if($action == 'add'){
            $OriginSocialGiftIds = ($session->getOriginSocialGiftIds() ? $session->getOriginSocialGiftIds() : array());
            // check product in list gift?, after remove add to list gift again
            if (in_array($product_id, $OriginSocialGiftIds) && !in_array($product_id, $SocialGiftIds)) {
                array_push($SocialGiftIds, $product_id);

                // update number gift can add
                $number_social_gift = $session->getNumberSocialGift();
                if ( $number_social_gift && $number_social_gift > 0) {
                    $session->setNumberSocialGift($number_social_gift - 1);
                }
            }
        }else if($action == 'remove'){
            // remove this product id from social list gift and update social list gift
            unset($SocialGiftIds[array_search($product_id, $SocialGiftIds)]);
            if(empty($SocialGiftIds)){
                $session->setGiftAddedFull(TRUE);
            }else{
                $session->setGiftAddedFull(FALSE);
            }
            Mage::getSingleton("checkout/session")->getMessages(true);
            $message = Mage::helper('mw_socialgift')->__('Gift was successfully added.');
            Mage::getSingleton('checkout/session')->addSuccess('<span class="sg_add_gift_success">'.$message.'<span>');
        }else{
            Mage::getSingleton('core/session')->addError('Update error'); 
            return;
        }
        // update list gift ids

        $session->setSocialGiftIds($SocialGiftIds);

        return;
    }
// @TODO: test again
    public function resetGiftSession()
    {
        // clear session
        $session = Mage::getSingleton('checkout/session');
        $session->setSocialGiftIds($session->getOriginSocialGiftIds());
        $session->setSocialGiftStatus('note_share');
        $session->unsetSocialProductShared();
    }
    public function saleOrderPlaceAfterUpdateTimeUsed()
    {
        // clear session
        $session = Mage::getSingleton('checkout/session');
        $session->setSocialGiftIds($session->getOriginSocialGiftIds());
        $session->setSocialGiftStatus('note_share');
        $session->unsetSocialProductShared();
        // update times_used:
        $model = Mage::getModel('mw_socialgift/salesrule');
        $rule_id = $model->getCollection()->getFirstItem()->getId();
        $times_used = $model->getCollection()->getFirstItem()->getTimesUsed();
        $data = array('times_used'=> $times_used + 1);
        $model->load($rule_id)->addData($data);
        try {
                $model->setId($rule_id)->save();
                // echo "Data updated successfully.";
            } catch (Exception $e){
                echo $e->getMessage(); 
        }
    }

    public function updateTableReport()
    {
        $model = Mage::getModel('mw_socialgift/salesrule');
        $rule_id = $model->getCollection()->getFirstItem()->getId();
        $data = array('rule_id'=> $rule_id,'time_created'=> time());
        $model = Mage::getModel('mw_socialgift/reports')->setData($data);
        try {
                $insertId = $model->save()->getId();
                // echo "Data successfully inserted. Insert ID: ".$insertId;
            } catch (Exception $e){
                echo $e->getMessage();   
        }
    }

    public function checkEmptyCart()
    {
        $totalItemsInCart = Mage::helper('checkout/cart')->getItemsCount();
        if ($totalItemsInCart == 0) {
            $session = Mage::getSingleton('checkout/session');
            $this->resetGiftSession();
            $session->setNumberSocialGift(0);
            Mage::getSingleton('checkout/session')->setRedirectUrl(Mage::getUrl('checkout/cart'));  
        }
        return;
    }
}