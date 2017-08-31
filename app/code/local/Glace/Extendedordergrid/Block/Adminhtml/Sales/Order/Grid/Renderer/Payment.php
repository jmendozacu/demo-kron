<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */ 
class Glace_Extendedordergrid_Block_Adminhtml_Sales_Order_Grid_Renderer_Payment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $index = $this->getColumn()->getIndex();
        $method = $row->getData($index);
        $instance = Mage::helper('payment')->getMethodInstance($method);
        return $instance ? $instance->getTitle() : '-';
    }
}