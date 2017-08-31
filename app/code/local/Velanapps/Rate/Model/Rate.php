<?php
 
class Velanapps_Rate_Model_Rate extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('rate/rate');
    }
}