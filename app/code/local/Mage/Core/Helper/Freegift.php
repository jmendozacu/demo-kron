<?php
class Mage_Core_Helper_Freegift extends Mage_Core_Helper_Abstract
{
	public function moduleEnabled()
	{
		if(Mage::helper('core')->isModuleEnabled('MW_FreeGift'))  {
			return Mage::getStoreConfig('freegift/config/enabled');
		}
		return 0;
	}
	
	public function renderFreeGiftLabel($_product)
	{
		if(Mage::helper('core')->isModuleEnabled('MW_FreeGift'))
			return Mage::helper('freegift')->renderFreeGiftLabel($_product);
		else return '';
	}
	
	public function renderFreeGiftCatalogList($_product)
	{
		if(Mage::helper('core')->isModuleEnabled('MW_FreeGift'))
			return Mage::helper('freegift')->renderFreeGiftCatalogList($_product);
		else return '';
	}
}