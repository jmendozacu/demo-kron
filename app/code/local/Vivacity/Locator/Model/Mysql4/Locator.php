<?php
class Vivacity_Locator_Model_Mysql4_Locator extends Mage_Core_Model_Mysql4_Abstract
{
     public function _construct()
     {
         $this->_init('locator/locator', 'locator_id');
     }
}
