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
 * Hideaddress Model
 * 
 * @category    Magestore
 * @package     Magestore_Hideaddress
 * @author      Magestore Developer
 */
class Simi_Hideaddress_Model_Hideaddress_Show extends Simi_Hideaddress_Model_Hideaddress {

    function getAddressShow() {
        if (Mage::getStoreConfig('hideaddress/general/enable') == 0) {
            $information = $this->statusError(array('Extesnion was disabled'));
            return $information;
        }
        $addresss = Mage::getModel('hideaddress/config')->toAddressArray();

        $data = array();
       
        $options = Mage::getResourceSingleton('customer/customer')->getAttribute('gender')->getSource()->getAllOptions();
        $values = array();
        foreach ($options as $option) {
            if ($option['value']) {
                $values[] = array(
                    'label' => $option['label'],
                    'value' => $option['value'],
                );
            }
        }
         
        foreach ($addresss as $address) {
            $path = "hideaddress/field_hide_management/" . $address['value'];
            $value = Mage::getStoreConfig($path);
            if (!$value || $value == null || !isset($value))
                $value = 3;
            if($value==1)
            $data[$address['value']] = "req";
            else if($value==2)
            $data[$address['value']] = "opt";
            else if($value==3)
            $data[$address['value']] = "";           
        }
        $data['gender_value']=$values;
        $datas = array();
        $datas[] = $data;
        $information = $this->statusSuccess();
        $information['data'] = $datas;
        return $information;
    }

    function getTerm() {
        if (Mage::getStoreConfig('hideaddress/general/enable') == 0) {
            $information = $this->statusError(array('Extesnion was disabled'));
            return $information;
        }
        $show = Mage::getStoreConfig('hideaddress/terms_conditions/enable_terms');
        if (!$show) {
            $information = $this->statusSuccess();
            $information['data'] = array();
            return $information;
        }

        $data = array();
        $term_title = Mage::getStoreConfig('hideaddress/terms_conditions/term_title');
        $term_html = Mage::getStoreConfig('hideaddress/terms_conditions/term_html');
//        $enable_custom_size = Mage::getStoreConfig('hideaddress/terms_conditions/enable_custom_size');
//        $width = Mage::getStoreConfig('hideaddress/terms_conditions/term_width');
//        $height = Mage::getStoreConfig('hideaddress/terms_conditions/term_height');
//        $enable_title="Yes";
//        if(!$enable_custom_size) {
//            $width='0';
//            $height='0';  
//            $enable_title="No";
//        }
        $data['title'] = $term_title;
        $data['content'] = $term_html;
//        $data['enable_size'] = $enable_title;
//        $data['width'] = $width;
//        $data['height'] = $height;

        $datas = array();
        $datas[] = $data;
        $information = $this->statusSuccess();
        $information['data'] = $datas;
        return $information;
    }

    public function checkBillingAddress($data){
       $adds = Mage::getModel('hideaddress/config')->toRespondArrray();
       $result=array();
        if(!isset($data->billingAddress->address_id) && $data->billingAddress->address_id!=0 && $data->billingAddress->address_id!=-1){     
            foreach ($adds as $add) {
                $path = "hideaddress/field_hide_management/" . $add['value'];
                $value = Mage::getStoreConfig($path);
                if (!$value || $value == null || !isset($value))
                    $value = 3;
                if ($value == 1) { //required
                    if($data->billingAddress->$add['respond']==null || !isset($data->billingAddress->$add['respond'])){
                        echo "   ".$add['respond'];
                        $result[]=$add['label']." is missing.";
                    }
                }
            }
        }
            return $result;
   }
    public function checkShippingAddress($data){
       $adds = Mage::getModel('hideaddress/config')->toRespondArrray();
       $result=array();
      if(!isset($data->shippingAddress->address_id) && $data->shippingAddress->address_id!=0 && $data->shippingAddress->address_id!=-1){
            foreach ($adds as $add) {
                $path = "hideaddress/field_hide_management/" . $add['value'];
                $value = Mage::getStoreConfig($path);
                if (!$value || $value == null || !isset($value))
                    $value = 3;
                if ($value == 1) { //required
                    if($data->shippingAddress->$add['respond']==null || !isset($data->shippingAddress->$add['respond'])){
                        echo "   ".$add['respond'];
                        $result[]=$add['label']." is missing.";
                    }
                }
            }
      }
            return $result;
   }
   public function checkDataRequired($data){
            $billingMiss=$this->checkBillingAddress($data);
            $shippingMiss=$this->checkShippingAddress($data);
            
            $information=array();
            if($billingMiss!=null || $shippingMiss!=null) {
                $results=array();
                $result = array_merge($billingMiss, $shippingMiss);
				$result = array_unique($result);
                $results['message']=$result;
                $information = $this->statusError($results['message']);
            }
            return $information;
   }
}