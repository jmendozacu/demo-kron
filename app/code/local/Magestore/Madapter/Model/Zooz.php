<?php

class Magestore_Madapter_Model_Zooz extends Mage_Payment_Model_Method_Abstract {

    protected $_code = 'zooz';    
    protected $_infoBlockType = 'madapter/zooz';
	
	// public function getOrderPlaceRedirectUrl()
    // {
          // return Mage::getUrl('paypal/standard/redirect', array('_secure' => true));
    // }
}
