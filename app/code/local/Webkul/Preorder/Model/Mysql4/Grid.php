<?php

class Webkul_Preorder_Model_Mysql4_Grid extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('preorder/grid', 'entity_id');
    }
}