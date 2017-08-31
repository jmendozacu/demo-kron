<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */ 
class Glace_Extendedordergrid_Block_Adminhtml_Settings_Tab_Main extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        $this->setTemplate('ciextendedordergrid/main.phtml');
    }
    
    public function getAttributes()
    {
        return Mage::getModel('ciextendedordergrid/order_item')->getAttributes();
    }
    
    public function getMappedColumns()
    {
        return Mage::getModel('ciextendedordergrid/order_item')->getMappedColumns();
    }
    
    public function getTabLabel()
    {
        return Mage::helper('ciextendedordergrid')->__('Attributes Configuration');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('ciextendedordergrid')->__('Attributes Configuration');
    }
    
    public function canShowTab()
    {
        return true;
    }
    
    public function isHidden()
    {
        return false;
    }

}
?>