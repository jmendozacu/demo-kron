<?php
class Devinc_Groupdeals_Block_Subscriber extends Mage_Core_Block_Template
{	
	public function getCity() {		
		$city = Mage::helper('groupdeals')->getCity();
		$region = Mage::helper('groupdeals')->getRegion();
		$groupdealCollection = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('city', $city)->addFieldToFilter('region', $region);
		
		if (count($groupdealCollection)>0) {
			return $city;
		} else {
			return false;
		}
	} 
}