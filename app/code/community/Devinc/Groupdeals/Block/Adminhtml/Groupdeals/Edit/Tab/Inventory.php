<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Tab_Inventory extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('groupdeals/product/tab/inventory.phtml');
    }
	
    public function getMinimumQty()
    {
		if (Mage::registry('groupdeals_data')->getMinimumQty()!='') {
			return Mage::registry('groupdeals_data')->getMinimumQty();
		} else {
			return 0;
		}
    }
	
    public function getMaximumQty()
    {
		if (Mage::registry('groupdeals_data')->getMaximumQty()!='') {
			return Mage::registry('groupdeals_data')->getMaximumQty();
		} else {
			return 1;
		}
    }

}
