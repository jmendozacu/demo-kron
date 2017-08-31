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
 * Connector Helper
 * 
 * @category 	Simi
 * @package 	Simi_Connector
 * @author  	Simi Developer
 */
class Simi_Connector_Helper_Customer extends Mage_Core_Helper_Abstract {

    public function defaultAdress($address) {
        return array(
            'firstname' => $address->getFirstName(),
            'lastname' => $address->getLastName(),
            'company' => $address->getCompany(),
            'street' => $address->getStreet(),
            'city' => $address->getCity(),
            'region_id' => $address->getRegionId(),
            'region' => $address->getRegion(),
            'postcode' => $address->getPostcode(),
            'country_id' => $address->getCountryId(),
            'telephone' => $address->getTelephone(),
            'use_for_shipping' => 0,
        );
    }

    public function getAddress($data, $customer) {
        return array(
            'address_fullname' => $customer->getName(),
            'address_street' => $data->getStreet(),
            'address_city' => $data->getCity(),
            'address_state' => $data->getRegionId(),
            'address_zip' => $data->getPostcode(),
            'address_country' => $data->getCountryId(),
            'address_phone' => $data->getTelephone(),
            'adddress_email' => $customer->getEmail(),
        );
    }

    public function getAddressToOrder($data, $customer, $address_billing_id, $address_shipping_id, $id = '') {
        $street = $data->getStreet();		
        return array(
            'address_id' => $id,
            'name' => $data->getFirstname(). " ". $data->getLastname(),
			'prefix' => $data->getPrefix() != NULL ? $data->getPrefix() : "",
			'suffix' => $data->getSuffix() != NULL ? $data->getSuffix() : "",
			'taxvat' => $data->getVatId() != NULL ? $data->getVatId() : "",
            'street' => $street[0],
            'city' => $data->getCity(),
            'state_name' => $data->getRegion(),
            'state_id' => $data->getRegionId(),
            'state_code' => $data->getRegionCode(),
            'zip' => $data->getPostcode(),
            'country_name' => $data->getCountryModel()->loadByCode($data->getCountry())->getName(),
            'country_code' => $data->getCountry(),
            'phone' => $data->getTelephone(),
            'email' => $customer->getEmail(),
        );
    }

    public function convertDataAddress($data, $state_id) {
        $name = Mage::helper('connector/checkout')->soptName($data->name);
        $address = array();
        $address['firstname'] = $name['first_name'];
        $address['lastname'] = $name['last_name'];
        $address['company'] = '';
        $address['street'] = array($data->street, '');
        $address['city'] = $data->city;
        $address['region_id'] = $state_id;
        $address['region'] = $data->state_name;
        $address['postcode'] = $data->zip;
        $address['country_id'] = $data->country_code;
        $address['country_name'] = $data->country_name;
        $address['telephone'] = $data->phone;
        $address['fax'] = '';
        $address['taxvat'] = isset($data->taxvat) == true ? $data->taxvat : '';
        $address['prefix'] = isset($data->prefix) == true ? $data->prefix : '';
        $address['suffix'] = isset($data->suffix) == true ? $data->suffix : '';
        $address['dob'] = isset($data->dob) == true ? $data->dob : '';
        $address['gender'] = isset($data->gender) == true ? $data->gender : Mage::getResourceSingleton('customer/customer')->getAttribute('gender')->getSource()->getOptionId('Male');
        $address['month'] = isset($data->month) == true ? $data->month : '';
        $address['day'] = isset($data->day) == true ? $data->day : '';
        $address['year'] = isset($data->year) == true ? $data->year : '';
        $address['email'] = $data->email;
        return $address;
    }

}