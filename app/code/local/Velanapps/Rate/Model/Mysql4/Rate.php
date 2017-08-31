<?php
 
class Velanapps_Rate_Model_Mysql4_Rate extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('rate/rate', 'id');
    }
}