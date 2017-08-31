<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */ 
class Glace_Extendedordergrid_Model_Order_Config
{
    function toOptionArray() {
        $ret = array();
        $statuses =  Mage::getSingleton('sales/order_config')->getStatuses();
        foreach($statuses as $value => $label)
            $ret[] = array(
                'value' => $value,
                'label' => $label
            );
        return $ret;
    }
}
?>