<?php

/**
 * @category    Mycompany
 * @package     Northsails_Storelocator
 * @author      Kalpesh Patel <kalpesh@orioncoders.com>
 */
class Sp_Pay4leter_Model_Pay4leter extends Mage_Core_Model_Abstract {

    public function _construct() {
        $this->_init('sp_pay4leter/pay4leter');
    }

    public function getListCollection() {
        $collection = $this->getCollection('sp_pay4leter/pay4leter_collection');
        return $collection;
    }

}
