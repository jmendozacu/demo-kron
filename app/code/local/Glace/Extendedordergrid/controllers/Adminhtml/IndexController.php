<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */ 
class Glace_Extendedordergrid_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function viewAction()
    {
        $order_id = $this->getRequest()->getParam("order_id");
        
        $block = $this->getLayout()->createBlock('ciextendedordergrid/adminhtml_order_view');
        
        $block->setData('order_id', $order_id);

        $this->getResponse()->setBody($block->toHtml());
    }
}