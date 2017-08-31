<?php

class Sp_Pay4leter_Model_Resource_Pay4leter extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('sp_pay4leter/pay4leter', 'plan_id');
    }
}

