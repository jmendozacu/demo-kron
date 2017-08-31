<?php

class MW_FreeGift_Block_Adminhtml_Quote extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_quote';
        $this->_blockGroup = 'freegift';
        $this->_headerText = Mage::helper('salesrule')->__('Manage Rules');
        $this->_addButtonLabel = Mage::helper('salesrule')->__('Add New Rule');
        parent::__construct();
    }
}
