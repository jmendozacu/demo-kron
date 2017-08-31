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
 * @package     Magestore_Ztheme
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Ztheme Helper
 * 
 * @category    Magestore
 * @package     Magestore_Ztheme
 * @author      Magestore Developer
 */
class Simi_Ztheme_Helper_Data extends Mage_Core_Helper_Abstract {

    public function addSpotproduct($installer) {
        $model = Mage::getModel('ztheme/spotproduct')->getCollection()->getFirstItem();
        $spotproducts = Mage::getModel('ztheme/config')->toOptionArray();
        $key = Mage::getModel('ztheme/config')->toKeySpotArray();
        if ($model->getData('spotproduct_id') == null) {
            $nSpot = count($spotproducts);
            for ($i = 0; $i < $nSpot; $i++) {
                $query = "INSERT INTO `{$installer->getTable('ztheme_spotproduct')}` (`position`,`spotproduct_name`,`spotproduct_key`,`status`)
                    VALUES (" . ($i + 1) . ",'" . $spotproducts[$i]['label'] . "','" . $key[$i] . "',1);";
                $installer->run($query);
            }
        }
    }
}
