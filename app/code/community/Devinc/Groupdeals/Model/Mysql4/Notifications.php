<?php

class Devinc_Groupdeals_Model_Mysql4_Notifications extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('groupdeals/notifications', 'notification_id');
    }
}