<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */ 
class Glace_Extendedordergrid_Block_Adminhtml_Sales_Order_Grid_Renderer_Address_Billing extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $entityId = $row->getData('entity_id');
        
        $billingAddress = Mage::getModel("sales/order")->load($entityId)->getBillingAddress();
        
        return $billingAddress ? $billingAddress->getFormated(true) : '';   
    }
}
?>