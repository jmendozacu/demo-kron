<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */ 
class Glace_Extendedordergrid_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function hasProcessingErrors(){
        
        $collection = Mage::getModel("ciextendedordergrid/order_item")->getUnmappedOrders();
        return count($collection->getItems()) > 0;
    }
    
}
?>