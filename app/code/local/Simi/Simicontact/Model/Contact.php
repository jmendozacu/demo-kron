<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_Simicontact
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Simicontact Helper
 * 
 * @category 	Magestore
 * @package 	Magestore_Simicontact
 * @author  	Magestore Developer
 */
class Simi_Simicontact_Model_Contact extends Simi_Connector_Model_Abstract {

    public function getContacts() {
        if ($this->getConfig("enable") == 0) {
            $information = $this->statusError(array('Extesnion was disabled'));
            return $information;
        }
        $data = array(
            'email' => $this->_getEmails(),
            'phone' => $this->_getPhoneNumbers(),
            'website' => $this->getConfig("website"),
            'style' => $this->getConfig("style"),
            'activecolor' => $this->getConfig("icon_color")
        );
        $information = $this->statusSuccess();
        $information['data'] = array($data);
        return $information;
    }

    public function _getPhoneNumbers() {
        return explode(",", str_replace(' ', '', $this->getConfig("phone")));
    }
    
    public function _getEmails() {
        $emails = explode(",", str_replace(' ', '', $this->getConfig("email")));
        foreach ($emails as $index=>$email) {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
                unset($emails[$index]);
        }
        return $emails;
    }

    public function getConfig($value) {
        return Mage::getStoreConfig("simicontact/general/" . $value);
    }

}
