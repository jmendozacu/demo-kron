<?php

class Devinc_Groupdeals_Model_Subscribers extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('groupdeals/subscribers');
    }
}