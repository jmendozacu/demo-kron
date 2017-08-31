<?php
class MW_SocialGift_Block_Adminhtml_Quote extends Mage_Adminhtml_Block_Widget_Grid_Container{

    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_quote'; // path of block
        $this->_blockGroup = 'mw_socialgift';   // block tag alias
        //create block: simplenews/adminhtml_simplenews_grid
        $this->_headerText = Mage::helper('mw_socialgift')->__('Manage Rules');
        $this->_addButtonLabel = Mage::helper('mw_socialgift')->__('Add New Rule');
    }

    protected function _construct()
    {
        parent::_construct();
    }
}
