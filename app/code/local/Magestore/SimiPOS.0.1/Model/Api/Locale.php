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
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * SimiPOS Category API Model
 * Use to call api with prefix: locale
 * Methods:
 *  country
 *  region
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Locale extends Magestore_SimiPOS_Model_Api_Abstract
{
    /**
     * 
     * @return array
     */
    public function apiCountry()
    {
        $result = array();
        $collection = Mage::getResourceModel('directory/country_collection');
        $result['total'] = $collection->count();
        foreach ($collection as $country) {
            $result[$country->getId()] = array(
                'name'  => $country->getName(),
            );
        }
        return $result;
    }
    
    /**
     * 
     * @param string $countryId Two letter code format
     * @return array
     */
    public function apiRegion($countryId)
    {
        $result = array();
        $arrRegions = Mage::getResourceModel('directory/region_collection')
            ->addCountryFilter($countryId);
        $result['total'] = $arrRegions->count();
        foreach ($arrRegions as $region) {
            $result[$region->getId()] = array(
                'name'      => $region->getName(),
            );
        }
        return $result;
    }
}
