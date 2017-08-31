<?php
class Velanapps_EasyProductSalesCount_Block_Show extends Mage_Core_Block_Template
{
	
	public function soldProducts()
	{
		$id = $this->getId();
		$items = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('product_id', $id);
		$count = 0;
		foreach ($items as $item)
		{

			$count += $item->getQtyOrdered() - $item->getQtyCanceled() - $item->getQtyRefunded();
		}
		return $count+(self::getInstore());
	}
	
	public function getStatus()
	{		
		$id = $this->getId();
		return Mage::getModel('catalog/product')->load($id)->getVaProductSalesCountShow();
	}
	public function getInstore()
	{
		$id = $this->getId();
		return Mage::getModel('catalog/product')->load($id)->getVaProductSalesCount();
	}
	
	

	public function getAccess()
	{
		return Mage::getStoreConfig('activation_tab/active_group/activation_key');
	
	}
	
	

}
