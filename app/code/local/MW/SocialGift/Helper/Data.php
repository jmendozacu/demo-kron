<?php
class MW_SocialGift_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * Path to store config if front-end output is enabled
     *
     * @var string
     */
    const XML_PATH_ENABLED            = 'socialgift/config/enabled';
    const MYCONFIG                    = 'socialgift/config/enabled';
    const XML_PATH_SHOW_SG_PROMOTION  = 'socialgift/config/showsocialgiftpromotion';
    const XML_PATH_VERSION_CF         = 'socialgift/config/versionfg';
    const XML_PATH_BOX_TITLE          = 'socialgift/config/boxtitle';
    const XML_PATH_DESCRIPTION        = 'socialgift/config/description';
    
    const XML_PATH_FB_ID       	      = 'socialgift/social/facebookid';
    const XML_PATH_FACEBOOK_ENABLE    = 'socialgift/social/facebook_enable';
    const XML_PATH_GOOGLEPLUS_ENABLE  = 'socialgift/social/googleplus_enable';
    const XML_PATH_TWITTER_ENABLE     = 'socialgift/social/twitter_enable';

    /**
     * Checks whether news can be displayed in the frontend
     *
     * @param integer|string|Mage_Core_Model_Store $store
     * @return boolean
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }    

    public function getBoxTitle($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_BOX_TITLE, $store);
    }       

    public function getDescription($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_DESCRIPTION, $store);
    }     

    public function getFBID($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_FB_ID, $store);
    }     

    public function isFBEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_FACEBOOK_ENABLE, $store);
    }    

    public function isGPEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_GOOGLEPLUS_ENABLE, $store);
    }  

    public function isTWEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_TWITTER_ENABLE, $store);
    }

    public function showIn($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_SHOW_SG_PROMOTION, $store);
    }

    public function getRuleByFreeProductId($productId = 0)
    {
        if ($productId > 0) {
            $session = Mage::getSingleton('checkout/session');
            $ProductsByRule = $session->getProductsByRule();
            foreach ($ProductsByRule as $key => $value) {
                if (in_array($productId, $value)) {
                    return $key;
                }
            }
        }
    }

    public function _getCountry()
    {
        $session = Mage::getSingleton('customer/session');
        if ($session->isLoggedIn()) {
            $customer = $session->getCustomer();
            $basedOn = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON);
            if(isset($basedOn)){
                if($basedOn = 'billing' && $customer->getDefaultBillingAddress()){
                    $country = $customer->getDefaultBillingAddress()->getCountryId();
                }else if($basedOn = 'shipping' && $customer->getDefaultShippingAddress()){
                    $country = $customer->getDefaultShippingAddress()->getCountryId();
                }else{
                    $country = (Mage::getStoreConfig('general/country/default') ? Mage::getStoreConfig('general/country/default') : 'default');
                }
            }
        }else{
            $country = (Mage::getStoreConfig('general/country/default') ? Mage::getStoreConfig('general/country/default') : 'default');
        }
        return $country;
    }

    public function getAvailableSocialGiftByCountry($country = 'default')
    {
        Mage::log($country);
        if ($country != 'default') {
            $collection      = $this->_getCollection();

            $quote           = Mage::getSingleton('checkout/session')->getQuote();
            $websiteId       = Mage::app()->getStore($quote->getStoreId())->getWebsiteId();
            //$customerGroupId = ($quote->getCustomerGroupId() ? $quote->getCustomerGroupId() : 0);
            Mage::log(Mage::getSingleton('customer/session')->getCustomerGroupId());
            Mage::log($quote->getCustomerGroupId());
            $customerGroupId = (Mage::getSingleton('customer/session')->getCustomerGroupId() ? Mage::getSingleton('customer/session')->getCustomerGroupId() : 0);

            $collection->setValidationFilter($websiteId, $customerGroupId);
            $collection->getSelect()
                        ->where('FIND_IN_SET( ?, sg_countries )', $country);
            $result = $collection->getSize();
        } else {
            $result = 0;
        }
        // echo $collection->getSize();
        // $collection->printLogQuery(TRUE,TRUE);
        return $result;
    }

    protected function _getCollection()
    {
        return  Mage::getResourceModel('mw_socialgift/salesrule_collection');
    }
    
    public function getRuleDataById($rule_id = 0)
    {
        if ($rule_id > 0) {
            $ruleData = $this->_getCollection()->addFieldToFilter('rule_id', array('eq' => $rule_id));
            return $ruleData->getFirstItem();
        }else{
            Mage::log("Error, cannot find rule.");
            return "Error";
        }
    }

    public function isAddToCartOn()
    {
        $session = Mage::getSingleton('checkout/session');
        $number_social_gift = $session->getNumberSocialGift();
        $number_of_free_gift = -1;
        $can_add_to_cart = FALSE;

        if (!$session->getNumberSocialGiftRule()) {
            $rule = $this->_getCollection();
            $rule = $rule->getFirstItem();
            $number_of_free_gift = $rule['number_of_free_gift'];
            $session->setNumberSocialGiftRule($rule['number_of_free_gift']);
            $session->setSGCountries($rule['sg_countries']);
        }else{
            $number_of_free_gift = $session->getNumberSocialGiftRule();
        }

        if (isset($number_social_gift) && ($number_social_gift >= 0) && ($number_social_gift < $number_of_free_gift)) {
            $can_add_to_cart = TRUE;
        }
        return $can_add_to_cart;
    }

    public function _getPriceByItem($Price = 0, $ruleAmount = 1, $simple_action = 'by_percent')
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

    public function myConfig()
    {
        return self::MYCONFIG;
    }

    public function disableConfig()
    {
        Mage::getSingleton('core/config')->saveConfig(Mage::helper('mw_socialgift')->myConfig(),0);
        $websites  = Mage::getModel('core/website')->getCollection()->getData();
        foreach($websites as $row)
        {
            if($row['code']!="admin")
            Mage::getSingleton('core/config')->saveConfig(Mage::helper('mw_socialgift')->myConfig(),0,'websites',$row['website_id']);
        }         
        
       $stores  = Mage::getModel('core/store')->getCollection()->getData();
        foreach($stores as $row)
        {
            if($row['code']!="admin")
            Mage::getSingleton('core/config')->saveConfig(Mage::helper('mw_socialgift')->myConfig(),0,'stores',$row['store_id']);
        }
        Mage::getSingleton('core/config')->saveConfig(Mage::helper('mw_socialgift')->myConfig(),0);
    }

    function enableConfig()
    {
        Mage::getSingleton('core/config')->saveConfig(Mage::helper('mw_socialgift')->myConfig(),1);
        $websites  = Mage::getModel('core/website')->getCollection()->getData();
        foreach($websites as $row)
        {
            if($row['code']!="admin")
            Mage::getSingleton('core/config')->saveConfig(Mage::helper('mw_socialgift')->myConfig(),1,'websites',$row['website_id']);
        }         
        
       $stores  = Mage::getModel('core/store')->getCollection()->getData();
        foreach($stores as $row)
        {
            if($row['code']!="admin")
            Mage::getSingleton('core/config')->saveConfig(Mage::helper('mw_socialgift')->myConfig(),1,'stores',$row['store_id']);
        }
        Mage::getSingleton('core/config')->saveConfig(Mage::helper('mw_socialgift')->myConfig(),1);
    }

    public function checkVersion($str)
    {
        $a       = explode('.', $str);
        $modules = array_keys((array) Mage::getConfig()->getNode('modules')->children());
        if (in_array('Enterprise_Banner', $modules)) {
            if ($a[1] >= '12') {
                return "enterprise12";
            }
        } elseif (in_array('Enterprise_Enterprise', $modules)) {
            if ($a[1] <= '10') {
                return "enterprise10";
            }
        } else {
            if ($a[1] == '7' || $a[1] == '8') {
                return "mg1.7";
            }
            if ($a[1] == '6') {
                return "mg1.6";
            }
            if ($a[1] == '5') {
                return "mg1.5";
            }
			if ($a[1] == '4') {
                return "mg1.4";
            }

	        return "mg{$a[0]}.{$a[1]}";
        }
    }

    public function configjs(){
        $path_js = "mw_socialgift/js/".$_SERVER['SERVER_NAME']."-socialgift-config-".Mage::app()->getStore()->getCode().".js";
        return (Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$path_js); // media folder chmod 777
    }

    // public function configcss(){
    //     $path_css = "mw_socialgift/css/".$_SERVER['SERVER_NAME']."-customcss.new-".Mage::app()->getStore()->getCode().".css";
        
    //     return (Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$path_css);
    // }

}