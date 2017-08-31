<?php

class Devinc_Groupdeals_Model_Mysql4_Crc extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('groupdeals/crc', 'crc_id');
    }
}