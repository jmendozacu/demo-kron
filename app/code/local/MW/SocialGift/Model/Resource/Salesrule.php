<?php
class MW_SocialGift_Model_Resource_Salesrule extends Mage_Core_Model_Resource_Db_Abstract{

    protected function _construct()
    {
        $this->_init('mw_socialgift/salesrule', 'rule_id');
    }
}