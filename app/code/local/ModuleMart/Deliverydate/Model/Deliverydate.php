<?php

class ModuleMart_Deliverydate_Model_Deliverydate extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('deliverydate/deliverydate');
    }
}