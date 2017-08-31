<?php
class MW_SocialGift_Model_Resource_Reports_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract{

    protected function _construct()
    {
        $this->_init('mw_socialgift/reports');
    }

}