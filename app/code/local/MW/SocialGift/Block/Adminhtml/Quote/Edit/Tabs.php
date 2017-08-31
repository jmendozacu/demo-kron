<?php

class MW_SocialGift_Block_Adminhtml_Quote_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('socialgift_catalog_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('mw_socialgift')->__('Social Gift Rule'));
    }
}
