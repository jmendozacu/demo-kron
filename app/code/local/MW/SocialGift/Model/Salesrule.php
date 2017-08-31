<?php
class MW_SocialGift_Model_Salesrule extends Mage_Core_Model_Abstract {

    protected $_eventPrefix     = 'mw_socialgift';
    protected $_eventObject     = 'salesrule';

    protected function _construct()
    {
        // parent::_construct();
        $this->_init('mw_socialgift/salesrule');
    }

}