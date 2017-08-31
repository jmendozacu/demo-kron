<?php

class Webkul_Preorder_Model_Mysql4_Preorder extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the preorder_id refers to the key field in your database table.
        $this->_init('preorder/preorder', 'preorder_id');
    }
}