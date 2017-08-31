<?php

class MW_FreeGift_Block_Adminhtml_Quote_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('freegift_catalog_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('salesrule')->__('Free Gift Rule'));
    }
}
