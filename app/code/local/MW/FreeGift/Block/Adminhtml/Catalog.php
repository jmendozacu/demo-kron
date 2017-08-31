<?php
class MW_FreeGift_Block_Adminhtml_Catalog extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_catalog';
    $this->_blockGroup = 'freegift';
    $this->_headerText = Mage::helper('freegift')->__('Catalog Rules Manager');
    $this->_addButtonLabel = Mage::helper('freegift')->__('Add New Rule');
    $this->_addButton('apply_rule', array(
            'label'     => Mage::helper('freegift')->__('Apply Rules'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('adminhtml/freegift_catalog/applyrules') .'\')',
        ));
        
    parent::__construct();
  }
}