<?php

class Ebizmarts_BakerlooRestful_Model_Inventorydelta extends Mage_Core_Model_Abstract {

    public function _construct() {
        $this->_init('bakerloo_restful/inventorydelta');
    }

    public function loadByItemId($itemId = null) {
    	$this->load($itemId, 'inventory_item_id');

    	return $this;
    }

    public function loadByProductId($itemId = null) {
    	$this->load($itemId, 'product_id');

    	return $this;
    }

}