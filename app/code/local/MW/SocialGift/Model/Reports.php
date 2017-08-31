<?php
class MW_SocialGift_Model_Reports extends Mage_Core_Model_Abstract {

    protected $_eventPrefix     = 'mw_socialgift';
    protected $_eventObject     = 'reports';

    protected function _construct()
    {
        // parent::_construct();
        $this->_init('mw_socialgift/reports');
    }

}