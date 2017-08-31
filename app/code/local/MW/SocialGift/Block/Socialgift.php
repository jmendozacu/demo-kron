<?php
class MW_SocialGift_Block_Socialgift extends Mage_Core_Block_Template {

    protected $_free_product = array();

    public function _construct()
    {

    }

    /*public function _getCountry()
    {
        $session = Mage::getSingleton('customer/session');
        $country = (Mage::getStoreConfig('general/country/default') ? Mage::getStoreConfig('general/country/default') : 'default');
        if ($session->isLoggedIn()) {
            $customer = $session->getCustomer();
        } else {
            $customer = FALSE;
        }

        if ($customer === FALSE) {
            if (Mage::getStoreConfig('general/country/default')) {
                $country = Mage::getStoreConfig('general/country/default');
            } else {
                $country = 'default';
            }
        } else {

            $basedOn = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON);
            switch ($basedOn) {
                case 'billing' :
                    $billingAddress = $customer->getDefaultBillingAddress();
                    if ($billingAddress) {
                        $country = $billingAddress->getCountryId();
                    }
                    break;
                case 'shipping' :
                    $shippingAddress = $customer->getDefaultShippingAddress();
                    if ($shippingAddress) {
                        $country = $shippingAddress->getCountryId();
                    }
                    break;
                default:
                    $country = 'default';
            }
        }
        return $country;
    }*/

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * News collection
     *
     * @var Magentostudy_News_Model_Resource_News_Collection
     */
    protected $_salesruleCollection = null;

    /**
     * Retrieve news collection
     *
     * @return Magentostudy_News_Model_Resource_News_Collection
     */
    protected function _getCollection()
    {
        return  Mage::getResourceModel('mw_socialgift/salesrule_collection');
    }

    protected function _getCustomerGroupId()
    {
        return Mage::getSingleton('customer/session')->getCustomerGroupId();
    }

    protected function _getReportsCollection()
    {
        return  Mage::getResourceModel('mw_socialgift/reports');
    }

    public function getCollection()
    {
        $collection = $this->_getCollection();

        $quote           = Mage::getSingleton('checkout/session')->getQuote();
        $websiteId       = Mage::app()->getStore($quote->getStoreId())->getWebsiteId();
        $customerGroupId = $quote->getCustomerGroupId() ? $quote->getCustomerGroupId() : 0;
        $country = Mage::helper('mw_socialgift')->_getCountry();
        $collection->setValidationFilter($websiteId, $customerGroupId);
        if ($country != 'default') {
            $collection->getSelect()
                        ->where('FIND_IN_SET( ?, sg_countries )', $country);
        }
        $collection->getSelect()
                        ->where('times_used < uses_limit');

        $this->_salesruleCollection = $collection;

        return $this->_salesruleCollection;
    }

    public function getFreegiftIds() {
        $session = Mage::getSingleton('checkout/session');
        $SocialGiftIds = $session->getSocialGiftIds();
        $NumberSocialGiftRule = $session->getNumberSocialGiftRule();
        $GiftAddedFull = ($session->getGiftAddedFull() ? $session->getGiftAddedFull() : FALSE);
        $result = [];

        if(!$SocialGiftIds && $GiftAddedFull === FALSE) {
            $rules = $this->getCollection();
            $FreegiftIds = array();

            $product_ids = '';
            foreach($rules as $rule) {
                $product_ids .= $rule['gift_product_ids'];
                $product_ids .= ',';
                $product_by_rule[$rule->getId()] = explode(",",$rule['gift_product_ids']);
            }
            $product_ids = array_unique(array_filter(explode(",",$product_ids)));
            if (!empty($product_ids)) {
                $session->setSocialGiftIds($product_ids);
                $session->setOriginSocialGiftIds($product_ids);
                $result = $product_ids;
            }else{
                $result = array();
            }
            $session->setProductsByRule($product_by_rule);
        }else{
            $result = $SocialGiftIds;
        }
        return $result;
    }
}