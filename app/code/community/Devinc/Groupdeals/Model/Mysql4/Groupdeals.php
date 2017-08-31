<?php

class Devinc_Groupdeals_Model_Mysql4_Groupdeals extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the groupdeals_id refers to the key field in your database table.
        $this->_init('groupdeals/groupdeals', 'groupdeals_id');
    }
}