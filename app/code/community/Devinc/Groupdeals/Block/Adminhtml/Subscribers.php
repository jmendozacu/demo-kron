<?php
class Devinc_Groupdeals_Block_Adminhtml_Subscribers extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    parent::__construct();	
    $this->_controller = 'adminhtml_subscribers';
    $this->_blockGroup = 'groupdeals';
    $this->_headerText = Mage::helper('groupdeals')->__('Manage Subscribers');
    $this->_removeButton('add');
  }
}