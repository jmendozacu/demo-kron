<?php

class MW_FreeGift_Model_Mysql4_Product extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('freegift/product', 'rule_product_id');
    }
}