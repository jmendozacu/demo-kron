<?php
class Devinc_Groupdeals_Block_Product_List_Universaldeals extends Devinc_Groupdeals_Block_Product_List_Sidedeals
{
	public function getItems()
    {
		$crcId = $this->getRequest()->getParam('crc', false);
		if (!$crcId) {
			$product = Mage::registry('current_product');
			if (!$product) {
				return parent::getItems();
			} else if (!$this->getRequest()->getParam('groupdeals_id', false)) {
				return parent::getItems();				
			} else if (Mage::helper('groupdeals')->getCity()!='Universal') {
				return parent::getItems();				
			}
		} else {
			$crc = Mage::getModel('groupdeals/crc')->load($crcId);
			if ($crc->getCity()!='Universal') {
				return parent::getItems();
			}
		}
    }
	
	public function getCity() {		
		return 'Universal';
	}	
    
    public function getRegion() {	
		return '';
	} 
	
	public function getTitle() {
		return $this->__('Universal deals');
	}
	
	public function getCrcId() {
		$region = $this->getRegion();
		$city = $this->getCity();		
		$crc = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('region', $region)->addFieldToFilter('city', $city)->getFirstItem();
		$crcId = $crc->getId();	
		
		return $crcId;
	}
	
}