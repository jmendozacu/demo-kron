<?php
class Devinc_Groupdeals_Block_Adminhtml_Merchants extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_merchants';
    $this->_blockGroup = 'groupdeals';
    $this->_headerText = Mage::helper('groupdeals')->__('Merchants Manager');
    $this->_addButtonLabel = Mage::helper('groupdeals')->__('Add Merchant');
    parent::__construct();	
  }
}