<?php

class Velan_Pricecompare_Model_Mysql4_Productupdate extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('pricecompare/productupdate', 'id');
    }
}