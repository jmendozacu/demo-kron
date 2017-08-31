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
class Magestore_Madapter_Model_Country extends Mage_Core_Model_Abstract {

    public function getCountries() {
        $list = array();
        $countries = Mage::getModel('directory/country')->getCollection();
        foreach ($countries as $country) {
            $list[] = array(
                'country_code' => $country->getId(),
                'country_name' => $country->getName(),
            );
        }
        return $list;
    }

    public function getAllowedCountries() {
        $countries = Mage::getResourceModel('directory/country_collection')->loadByStore();
        foreach ($countries as $country) {
            $list[] = array(
                'country_code' => $country->getId(),
                'country_name' => $country->getName(),
            );
        }
        return $list;
    }

    public function getDefaultCountry() {
        $list = array();
        $country_code = Mage::getStoreConfig('general/country/default');
        $country = Mage::getModel('directory/country')->loadByCode($country_code);
        $locale = Mage::app()->getLocale()->getLocaleCode();
        $currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        $list = array(
            'country_code' => $country->getId(),
            'country_name' => $country->getName(),
            'locale_identifier' => $locale,
            'currency_symbol' => $currencySymbol,
        );
        return $list;
    }

    public function getCurrencySymbol() {
        $currencySymbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        return $currencySymbol;
    }

}