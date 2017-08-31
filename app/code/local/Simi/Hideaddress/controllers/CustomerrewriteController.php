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
 * @category 	Simi
 * @package 	Simi_Connector
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Mconnect Index Controller
 * 
 * @category 	Simi
 * @package 	Simi_Connector
 * @author  	Simi Developer
 */
class Simi_Hideaddress_CustomerrewriteController extends Simi_Connector_CustomerController {

    protected $_active = true;

    public function save_addressAction() {
        if (Mage::getStoreConfig('hideaddress/general/enable') == 1) {
            $data = $this->getData();
            if ($data && $data->store_id) {
                Mage::app()->getCookie()->set(Mage_Core_Model_Store::COOKIE_NAME, Mage::app()->getStore($data->store_id)->getCode(), TRUE);
                Mage::app()->setCurrentStore(
                        Mage::app()->getStore($data->store_id)->getCode()
                );
                Mage::getSingleton('core/locale')->emulate($data->store_id);
            }
            
        $data->address_id =  isset($data->address_id) == true ? $data->address_id : 'N/A';
        $data->name = isset($data->name) == true ? $data->name : 'N/A';
        $data->street = isset($data->street) == true ? $data->street : 'N/A';// array($data->street, 'N/A');
        $data->city = isset($data->city) == true ? $data->city : 'N/A';
        $data->company = isset($data->company) == true ? $data->company : 'N/A';
        $data->state_code = isset($data->state_code) == true ? $data->state_code : 'AL';
        $data->state_id = isset($data->state_id) == true ? $data->state_id : 0;
        $data->state_name = isset($data->state_name) == true ? $data->state_name : 'N/A';
        $data->zip = isset($data->zip) == true ? $data->zip : 'N/A';
        $data->country_code = isset($data->country_code) == true ? $data->country_code : 'US';
        $data->country_name = isset($data->country_name) == true ? $data->country_name : 'N/A';
        $data->phone = isset($data->phone) == true ? $data->phone : 'N/A';
        $data->email = isset($data->email) == true ? $data->email : 'N/A';
        $data->suffix = isset($data->suffix) == true ? $data->suffix : 'N/A';
        $data->prefix = isset($data->prefix) == true ? $data->prefix : 'N/A';
        $data->dob = isset($data->dob) == true ? $data->dob : 'N/A';
        $data->taxvat = isset($data->taxvat) == true ? $data->taxvax : 'N/A';
        $data->gender = isset($data->gender) == true ? $data->gender : Mage::getResourceSingleton('customer/customer')->getAttribute('gender')->getSource()->getOptionId('Male');
        $data->month = isset($data->month) == true ? $data->month : 'N/A';
        $data->day = isset($data->day) == true ? $data->day : 'N/A';
        $data->year = isset($data->year) == true ? $data->year : 'N/A';
        $data->fax = isset($data->fax) == true ? $data->fax : '';
      

            $this->setData($data);
            $information = Mage::getModel('connector/customer')->saveAddress($data);
            $this->_printDataJson($information);
        } else {
            parent::save_addressAction();
        }
    }

}