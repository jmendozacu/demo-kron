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
 * Rewrite Free Shipping
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Shipping extends Mage_Shipping_Model_Shipping
{
    /**
     * Get carrier by its code
     *
     * @param string $carrierCode
     * @param null|int $storeId
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getCarrierByCode($carrierCode, $storeId = null)
    {
        $activeCode = empty($this->_availabilityConfigField) ? 'active' : $this->_availabilityConfigField;
        if (!Mage::getStoreConfigFlag('carriers/'.$carrierCode.'/'.$activeCode, $storeId)) {
            if ($carrierCode != 'freeshipping') return false;
            if (!Mage::getSingleton('simipos/session')->getSessionId()
                || !Mage::getStoreConfigFlag('simipos/checkout/freeshipping', $storeId)
            ) {
                return false;
            }
        }
        $className = Mage::getStoreConfig('carriers/'.$carrierCode.'/model', $storeId);
        if (!$className) {
            return false;
        }
        $obj = Mage::getModel($className);
        if ($storeId) {
            $obj->setStore($storeId);
        }
        return $obj;
    }
}
