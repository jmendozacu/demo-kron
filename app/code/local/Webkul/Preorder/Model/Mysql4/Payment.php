<?php

class Webkul_Preorder_Model_Mysql4_Payment extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('preorder/payment', 'entity_id');
    }
}