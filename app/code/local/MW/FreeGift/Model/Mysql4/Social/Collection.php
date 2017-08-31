<?php
/**
 * Created by PhpStorm.
 * User: manhnt
 * Date: 10/24/14
 * Time: 3:10 PM
 */

class MW_FreeGift_Model_Mysql4_Social_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('freegift/social');
    }
}