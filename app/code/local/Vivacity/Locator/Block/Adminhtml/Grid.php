<?php
class Vivacity_Locator_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
     $this->_controller = 'adminhtml_locator';
     $this->_blockGroup = 'locator';
     $this->_headerText = 'List All Store';
     $this->_addButtonLabel = 'Add a store';
     parent::__construct();
     }
}
