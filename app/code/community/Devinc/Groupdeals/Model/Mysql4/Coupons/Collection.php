<?php

class Devinc_Groupdeals_Model_Mysql4_Coupons_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('groupdeals/coupons');
    }
}