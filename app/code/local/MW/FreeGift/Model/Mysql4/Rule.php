<?php

class MW_FreeGift_Model_Mysql4_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('freegift/rule', 'rule_id');
    }
}