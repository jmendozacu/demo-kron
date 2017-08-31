<?php

class Devinc_Groupdeals_Model_Mysql4_Subscribers extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('groupdeals/subscribers', 'subscriber_id');
    }
}