<?php
class Sp_Pay4leter_Model_Resource_Pay4leter_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('sp_pay4leter/pay4leter');
    }
}