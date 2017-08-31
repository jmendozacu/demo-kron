<?php
class Vivacity_Locator_Model_Mysql4_Locator_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
 {
     public function _construct()
     {
         parent::_construct();
         $this->_init('locator/locator');
     }
}
