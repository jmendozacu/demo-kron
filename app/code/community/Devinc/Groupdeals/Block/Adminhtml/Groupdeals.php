<?php
class Devinc_Groupdeals_Block_Adminhtml_Groupdeals extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_groupdeals';
        $this->_blockGroup = 'groupdeals';
        $this->_headerText = Mage::helper('groupdeals')->__('Deal Manager');
        $this->_addButtonLabel = Mage::helper('groupdeals')->__('Add Deal');
        	
        parent::__construct();	
		//Mage::getModel('groupdeals/groupdeals')->refreshGroupdeals();
    	if (!Mage::getModel('groupdeals/merchants')->getPermission('add_edit')) {
    		$this->removeButton('add');
    	}
    }
}