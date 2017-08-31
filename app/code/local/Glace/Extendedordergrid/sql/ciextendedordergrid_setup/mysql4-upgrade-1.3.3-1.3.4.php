<?php
 /*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */ 
    $installer = $this;
    $installer->startSetup();
    
    $orderItem = Mage::getModel("ciextendedordergrid/order_item");
    $attributes = $orderItem->getMappedColumns();
    
    $orderItem->mapData(array(), array(), TRUE);
    $orderItem->mapData($attributes, array(), TRUE);
    
    $installer->endSetup();
?>