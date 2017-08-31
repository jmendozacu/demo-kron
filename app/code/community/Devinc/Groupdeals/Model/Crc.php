<?php

class Devinc_Groupdeals_Model_Crc extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('groupdeals/crc');
    }
    
    public function getCrcCollection($_groupdealId) {
		return $this->getCollection()->addFieldToFilter('groupdeals_id', $_groupdealId);
    }
    
    public function getMainCrc($_groupdealId) {
		return $this->getCollection()->addFieldToFilter('groupdeals_id', $_groupdealId)->setOrder('crc_id', 'ASC')->getFirstItem();
    }
    
    public function getProductMainCrc($_productId) {
		return $this->getCollection()->addFieldToFilter('product_id', $_productId)->setOrder('crc_id', 'ASC')->getFirstItem();
    }
    
    public function getCitiesArray($_groupdealId) {
		$collection = $this->getCrcCollection($_groupdealId);
		
		$cities = array();
		if (count($collection)) {
			foreach ($collection as $item) {
				$cities[] = $item->getCity();
			}
		}
		
		return $cities;
    }
    
    public function getCitiesString($_groupdealId, $_separator = ',') {
		$cities = $this->getCitiesArray($_groupdealId);
		$citiesString = implode($_separator, $cities);	
		
		return $citiesString;
    }
    
    public function getDealCountryId($_groupdealId) {
		return $this->getCollection()->addFieldToFilter('groupdeals_id', $_groupdealId)->getFirstItem()->getCountryId();
    }
}