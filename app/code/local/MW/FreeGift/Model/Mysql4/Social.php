<?php
/**
 * Created by PhpStorm.
 * User: manhnt
 * Date: 10/24/14
 * Time: 3:07 PM
 */

class MW_FreeGift_Model_Mysql4_Social extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('freegift/social', 'id');
    }
}