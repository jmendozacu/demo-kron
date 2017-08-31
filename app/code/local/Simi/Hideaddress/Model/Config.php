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
 * @category    Magestore
 * @package     Magestore_Hideaddress
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Hideaddress Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_Hideaddress
 * @author      Magestore Developer
 */
class Simi_Hideaddress_Model_Config {

    public function toAddressArray() {
        return array(
           
            array('value' => 'company', 'label' => Mage::helper('core')->__('Company')),
            array('value' => 'street', 'label' => Mage::helper('core')->__('Street')),
            array('value' => 'country', 'label' => Mage::helper('core')->__('Country')),
            array('value' => 'state', 'label' => Mage::helper('core')->__('State')),
            array('value' => 'city', 'label' => Mage::helper('core')->__('City')),
            array('value' => 'zipcode', 'label' => Mage::helper('core')->__('ZipCode')),
            array('value' => 'telephone', 'label' => Mage::helper('core')->__('Telephone')),
            array('value' => 'fax', 'label' => Mage::helper('core')->__('Fax')),
            array('value' => 'prefix', 'label' => Mage::helper('core')->__('Prefix')),
           // array('value' => 'middlename', 'label' => Mage::helper('core')->__('Middlename')),
            array('value' => 'suffix', 'label' => Mage::helper('core')->__('Suffix')),
            array('value' => 'birthday', 'label' => Mage::helper('core')->__('Birthday')),
            array('value' => 'gender', 'label' => Mage::helper('core')->__('Gender')),
            array('value' => 'taxvat', 'label' => Mage::helper('core')->__('Taxvat')));
    }
    public function toRespondArrray(){
         return array(
           
            array('value' => 'company','respond'=>'company', 'label' => Mage::helper('core')->__('Company')),
            array('value' => 'street','respond'=>'street', 'label' => Mage::helper('core')->__('Street')),
            array('value' => 'country','respond'=>'country_code', 'label' => Mage::helper('core')->__('Country')),
            array('value' => 'state','respond'=>'state_code', 'label' => Mage::helper('core')->__('State')),
            array('value' => 'city','respond'=>'city', 'label' => Mage::helper('core')->__('City')),
            array('value' => 'zipcode','respond'=>'zip', 'label' => Mage::helper('core')->__('Zip Code')),
            array('value' => 'telephone','respond'=>'phone', 'label' => Mage::helper('core')->__('Phone')),
            array('value' => 'fax','respond'=>'fax', 'label' => Mage::helper('core')->__('fax')),
            array('value' => 'prefix','respond'=>'prefix', 'label' => Mage::helper('core')->__('Prefix')),
           // array('value' => 'middlename', 'label' => Mage::helper('core')->__('Middlename')),
            array('value' => 'suffix','respond'=>'suffix', 'label' => Mage::helper('core')->__('Suffix')),
            array('value' => 'birthday','respond'=>'year', 'label' => Mage::helper('core')->__('Birthday')),
            array('value' => 'gender','respond'=>'gender', 'label' => Mage::helper('core')->__('Gender')),
            array('value' => 'taxvat','respond'=>'taxvat', 'label' => Mage::helper('core')->__('Taxvat')));
    }

}