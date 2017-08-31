<?php

class MW_FreeGift_Model_Mysql4_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('freegift/product');
    }
    
	public function setValidationFilter($websiteId, $customerGroupId, $now=null)
    {
        if (is_null($now)) {
            $now = Mage::getModel('core/date')->date('Y-m-d');
        }

        $this->getSelect()->where('find_in_set(?, website_ids)', (int)$websiteId);
        $this->getSelect()->where('find_in_set(?, customer_group_ids)', (int)$customerGroupId);
        
        $this->getSelect()->where('from_date is null or from_date<=?', $now);
        $this->getSelect()->where('to_date is null or to_date="0000-00-00" or to_date="" or to_date>=?', $now);
        $this->getSelect()->order('sort_order');
        return $this;
    }
}