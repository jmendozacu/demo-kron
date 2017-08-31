<?php

class Devinc_Groupdeals_Model_Mysql4_Crc_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('groupdeals/crc');
    }

    /**
     * Convert items array to hash for select options
     *
     * @return Array
     */
    public function toOptionHash()
    {
        return $this->_toOptionHash('crc_id', 'city');
    }
}