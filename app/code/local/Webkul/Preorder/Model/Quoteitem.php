<?php
class Webkul_Preorder_Model_Quoteitem extends Mage_Core_Model_Abstract
{
    public function _construct() {
        parent::_construct();
        $this->_init('preorder/quoteitem');
    }
}