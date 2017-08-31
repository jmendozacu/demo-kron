<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */ 
class Glace_Extendedordergrid_Adminhtml_SettingsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        
        $this->_addContent($this->getLayout()->createBlock('ciextendedordergrid/adminhtml_settings'))
                ->_addLeft($this->getLayout()->createBlock('ciextendedordergrid/adminhtml_settings_tabs'))
                ->renderLayout();
    }
    
    public function processAction()
    {
        $attributes = $this->getRequest()->getParam('attribute', array());
        $orderItem = Mage::getModel("ciextendedordergrid/order_item");
        $codes = array_keys($attributes);
        
        $orderItem->mapData($codes, array(), TRUE);
        
        $backUrl = Mage::app()->getRequest()->getParam('backurl');
        if (!$backUrl)
        {
            $backUrl = $this->getUrl('*/adminhtml_settings/index');
        }
        
        $this->_getSession()->addSuccess($this->__('Add columns has been added.'));
        
        $unmappedOrders = $orderItem->getUnmappedOrders();
        foreach($unmappedOrders as $unmappedOrder){
            $label = $unmappedOrder->getIncrementId()." can't support, ". $unmappedOrder->getName() . " can't found";
            $this->_getSession()->addNotice($label);
        }
        
        if (count($unmappedOrders) > 0){
            $orderItem->clearTemporaryData();
        }
        
        
        $this->getResponse()->setRedirect($backUrl);
    }
}
?>