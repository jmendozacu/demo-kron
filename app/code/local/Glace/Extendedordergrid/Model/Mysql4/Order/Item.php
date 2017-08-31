<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */ 
class Glace_Extendedordergrid_Model_Mysql4_Order_Item extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('ciextendedordergrid/order_item', 'extendedordergrid_item_id');
    }
}