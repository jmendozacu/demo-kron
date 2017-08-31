<?php

class Webkul_Preorder_Model_Mysql4_Quote extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('preorder/quote', 'entity_id');
    }
}