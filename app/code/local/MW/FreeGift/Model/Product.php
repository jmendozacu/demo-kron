<?php
class MW_FreeGift_Model_Product extends Mage_Core_Model_Abstract
{
    protected $_freegift_products;
    protected $_applied_rules;
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('freegift/product');
    }
	
    public function init(Mage_Catalog_Model_Product $product)
    {
        $quote           = Mage::getSingleton('checkout/session')->getQuote();
        $websiteId       = Mage::app()->getStore($quote->getStoreId())->getWebsiteId();
        $customerGroupId = $quote->getCustomerGroupId() ? $quote->getCustomerGroupId() : 0;
        
        $collection = $this->getCollection()->setValidationFilter($websiteId, $customerGroupId)->addFieldToFilter('product_id', $product->getId());
        $collection->getSelect()->where('((discount_qty > times_used) or (discount_qty=0))');
        $collection->load();
        $this->_freegift_products = array();
        $this->_applied_rules     = array();
        if (sizeof($collection))
            foreach ($collection as $freegiftProduct) {
                $this->_freegift_products = array_merge(explode(",", $freegiftProduct->getData('gift_product_ids')), $this->_freegift_products);
                $this->_applied_rules[]   = $freegiftProduct->getRuleId();
                if ($freegiftProduct->getData('stop_rules_processing'))
                    break;
            }
        
        return $this;
    }
    
    public function getFreeGifts()
    {
        return sizeof($this->_freegift_products) ? $this->_freegift_products : false;
    }
    
    public function getAplliedRuleIds()
    {
        return sizeof($this->_applied_rules) ? $this->_applied_rules : false;
    }
}