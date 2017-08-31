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
 * Madapter Block
 * 
 * @category 	Simi
 * @package 	Simi_Connector
 * @author  	Simi Developer
 */
class Simi_Themeone_Model_Config_App extends Simi_Connector_Model_Abstract {

    public function getBannerList() {
        $list = Mage::getModel('themeone/banner')->getBannerList();
        if (count($list)) {
            $information = $this->statusSuccess();
            $information['data'] = $list;
            return $information;
        } else {
            $information = $this->statusError();
            return $information;
        }
    }
     public function getListPlugin($device_id) {
        $plugins = Mage::getModel('connector/plugin')->getListPlugin($device_id);
        $state=Mage::getStoreConfig('themeone/general/enable');
        if ($plugins->getSize()) {
            $data = array();
            foreach ($plugins as $plugin) {
//                if ($this->checkPlugin($plugin->getPluginSku())) {
     /*           if($plugin->getPluginSku()=="Simi_Themeone" && $state ==0) continue;
                    $data[] = array(
                        'name' => $plugin->getPluginName(),
                        'version' => $plugin->getPluginVersion(),
                        'sku' => $plugin->getPluginSku(),
                    );*/
//                }
            }
            $information = $this->statusSuccess();
            $information['data'] = $data;
            return $information;
        } else {
            $information = $this->statusError();
            return $information;
        }
    }
}