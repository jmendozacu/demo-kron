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
class Simi_Hideaddress_Model_Observer extends Simi_Connector_Model_Observer{

    /**
     * process controller_action_predispatch event
     *
     * @return Simi_Hideaddress_Model_Observer
     */
    
     public function paymentMethodIsActive($observer) {
       
        $result = $observer['result'];
        $method = $observer['method_instance'];
        //$store = $quote ? $quote->getStoreId() : null;            
        if ($method->getCode() == 'transfer_mobile') {
            if (Mage::app()->getRequest()->getControllerModule() != 'Simi_Connector' && Mage::app()->getRequest()->getControllerModule() != 'Simi_Hideaddress') {
                $result->isAvailable = false;
            }
        }
    }
    
    public function controllerActionPredispatch($observer) {
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }

    public function addCondition($observer) {
        $object = $observer->getObject();
        $data = $object->getCacheData();
          if (Mage::getStoreConfig('hideaddress/general/enable') == 0) { 
            $agreements = Mage::helper('connector/checkout')->getAgreements();
            $conditions = array();
            foreach ($agreements as $agreement) {
                if ($agreement->getIsHtml()) {
                    $conditions[] = array(
                        'id' => $agreement->getId(),
                        'name' => $agreement->getName(),
                        'title' => $agreement->getCheckboxText(),
                        'content' => $agreement->getContent(),
                    );
                } else {
                    $conditions[] = array(
                        'id' => $agreement->getId(),
                        'name' => $agreement->getName(),
                        'title' => $agreement->getCheckboxText(),
                        'content' => nl2br(Mage::helper('connector')->escapeHtml($agreement->getContent())),
                    );
                }
            }
            $data['condition'] = $conditions;
            $object->setCacheData($data, "simi_connector");
          } else if (Mage::getStoreConfig('hideaddress/general/enable') == 1) { 
            $show = Mage::getStoreConfig('hideaddress/terms_conditions/enable_terms');
            $conditions = array();
            if ($show) {
                $term_title = Mage::getStoreConfig('hideaddress/terms_conditions/term_title');
                $term_html = Mage::getStoreConfig('hideaddress/terms_conditions/term_html');
                $condition=array();
                $condition['id']=-1;
                $condition['name']="Terms and conditions";
                $condition['title'] = $term_title;
                $condition['content'] = $term_html;
                $conditions[]=$condition;
            }    
            $data['condition'] = $conditions;
            $object->setCacheData($data, "simi_connector");
        }
           
        return;
    }
}