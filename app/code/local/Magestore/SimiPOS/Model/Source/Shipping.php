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
 * SimiPOS Shipping Methods Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Source_Shipping
{
    public function toOptionArray()
    {
        $options = array();
        $carriers = Mage::getStoreConfig('carriers');
        foreach ($carriers as $code => $carrier) {
            $active = $carrier['active'];
            if ($active == 1 || $active == true || $code == 'freeshipping') {
                if (isset($carrier['title'])) {
                    $options[] = array(
                        'label' => $carrier['title'],
                        'value' => $code
                    );
                }
            }
        }
        return $options;
    }
}