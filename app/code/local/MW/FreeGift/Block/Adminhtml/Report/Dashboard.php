<?php

class MW_FreeGift_Block_Adminhtml_Report_Dashboard extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_report_dashboard';
        $this->_headerText = Mage::helper('freegift')->__('Dashboard');
        $this->_blockGroup = 'freegift';
        parent::__construct();
        $this->_removeButton('add');
    }
}