<?php
 
class Velanapps_Rate_Model_Mysql4_Rate_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        //parent::__construct();
        $this->_init('rate/rate');
    }
}