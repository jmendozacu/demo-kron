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
 * @package 	Magestore_Madapter
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Madapter Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @author  	Magestore Developer
 */
class Magestore_Madapter_Model_Device extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('madapter/device');
    }

    public function setDataDevice($data) {
        // if ($data['user_id'] <= 0) {
        $this->setData('device_token', $data['device_token']);
        // $this->setData('user_id', $data['user_id']);
        try {
            $this->save();
            return 'SUCCESS';
        } catch (Exception $e) {
            return 'FAIL';
        }
    }

}