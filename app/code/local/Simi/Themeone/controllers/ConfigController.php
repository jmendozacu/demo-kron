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
 * Connector Config Controller
 * 
 * @category 	Simi
 * @package 	Simi_Connector
 * @author  	Simi Developer
 */
class Simi_Themeone_ConfigController extends Simi_Connector_ConfigController {

    public function get_bannerAction() {
         $status=Mage::getStoreConfig('themeone/general/enable');            
             if($status) {
        $information = Mage::getModel('themeone/config_app')->getBannerList();
        $this->_printDataJson($information);
        }
        else{
            parent::get_bannerAction();
        }
    }
    
    // public function get_pluginsAction() {
     
        // $status=Mage::getStoreConfig('themeone/general/enable');  
        // echo $status;
             // if($status) {
                 // $device_id = $this->getDeviceId();
        // $information = Mage::getModel('themeone/config_app')->getListPlugin($device_id);
        // $this->_printDataJson($information);
        // }
        // else{
            // parent::get_pluginsAction();
        // }
    // }

}
