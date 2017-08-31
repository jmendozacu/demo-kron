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
 * @package     Magestore_ThemeOne
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * ThemeOne Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_ThemeOne
 * @author      Magestore Developer
 */
class Simi_Themeone_Model_Observer {

    /**
     *
     * @return Simi_Ztheme_Model_Observer
     */
    public function connectorConfigGetPluginsReturn($observer) {

        if ($this->getConfig("enable") == 0) {
              $observerObject = $observer->getObject();
              $observerData = $observer->getObject()->getData();
              $plugins = array();
              foreach ($observerData['data'] as $key => $plugin) {
              if ($plugin['sku'] == 'simi_themeone')
              continue;
              $plugins[] = $plugin;
              }
              $observerData['data'] = $plugins;
              $observerObject->setData($observerData);
        }
    }
    /**
     * process controller_action_predispatch event
     *
     * @return Simi_ThemeOne_Model_Observer
     */
    public function controllerActionPredispatch($observer) {
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }

    public function disablemodule($observer) {
        $status = Mage::getStoreConfig('themeone/general/enable');
        if ($status) {
            
           // $this->_disableModule('Simi_Spotproduct');
        }
       
    }
    public function configsave($observer){
        $status = Mage::getStoreConfig('themeone/general/enable');
         $inchooSwitch = new Mage_Core_Model_Config();
         if ($status) {
            $inchooSwitch ->saveConfig('themeone/general/disable', "0", 'default', 0);
        }
        else{
            $inchooSwitch ->saveConfig('themeone/general/disable', "1", 'default', 0);
        }
    }

    protected function _disableModule($moduleName) {
        // Disable the module itself

        $nodePath = "modules/$moduleName/active";
        if (Mage::helper('core/data')->isModuleEnabled($moduleName)) {
            Mage::getConfig()->setNode($nodePath, 'false', true);
        }

// Disable its output as well (which was already loaded)
        $outputPath = "advanced/modules_disable_output/$moduleName";
        if (!Mage::getStoreConfig($outputPath)) {
            Mage::app()->getStore()->setConfig($outputPath, true);
        }
    }

    public function changeBanners($observer){


    }

    public function changePlugins($observer){


    }

    public function getConfig($value) {
        return Mage::getStoreConfig("themeone/general/" . $value);
    }

}