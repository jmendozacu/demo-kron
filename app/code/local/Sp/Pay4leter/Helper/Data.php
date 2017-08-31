<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Contacts
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Contacts base helper
 *
 * @category   Northsails
 * @package    Northsails_Contacts
 * @author      OrionCoders <develpers@orioncoders.com>
 */
class Sp_Pay4leter_Helper_Data extends Mage_Core_Helper_Abstract
{
    public $settings = array();
    public $log = array();
    public $pay4leterConfigArr;
    public $apiKey;
    public $redirectUrl;
    public $cancelUrl;
    public $isActive;
    public $rankByKey;

    public function __construct() {

        /************************************* Configuration *******************/
        //$this->settings['api_key'] = Mage::getStoreConfig('pay4leter/pay4leter_configuration/pay4leter_api_key');
    }

    /**
    * read csv file
    */
    public function getCsvData($file){
        $csvObject = new Varien_File_Csv();
        try {
            return $csvObject->getData($file);
        } catch (Exception $e) {
            Mage::log('Csv: ' . $file . ' - getCsvData() error - '. $e->getMessage(), Zend_Log::ERR, 'exception.log', true);
            return false;
        }

    }

    /**
    * add records to plan table
    */
   public function addPlanData($file){

        $planData=$this->getCsvData($file);
        $titles = $planData[0];
        $conn = Mage::getModel('core/resource')->getConnection('core_write');
        $prefix = Mage::getConfig()->getTablePrefix();
        $newPlans = array();
        $planValues = Mage::helper('pay4leter/pay4laterplans')->getConfigValue();
        
        $planKeys = array_keys($planValues);
         $colcount= count($planData[0]);
        
            $i=0;
            foreach($planData as $key=>$plan){
                $new_plans = array();
                //  print_r($plan);exit;
                if($key > 0){

                    /* foreach($plan as $key1 => $val){

                        if($key1 > 2){
                            if($val == 1){
                               $product = Mage::getModel('catalog/product')->load($plan[0]);
                                if($product->getId()){
                                    if(!in_array($titles[$key1],$newPlans)){
                                        $newPlans[] = $titles[$key1];
                                    }
                                    $planModel = Mage::getModel('sp_pay4leter/pay4leter');
                                    $where = array('product_id = ?' => $plan[0],
                                                   'plan_name = ?' => $titles[$key1]
                                                   );
                                    $conn->delete($prefix.'pay4leterProductPlans',$where);

                                    //Insert new records
                                    $planDetails = array(
                                        'product_id' => $plan[0],
                                        'plan_name' => $titles[$key1],
                                        'plan_codes' => $plan[$key1+1],
                                        'enabled'=>1
                                        );
                                
                                    $planModel->setData($planDetails)->save();

                                    $this->importInfo("Product id==>".$plan[0]."   plan==>".$titles[$key1],' == ',$val.' Saved');
                                }
                            }
                        }
                    }*/

                    foreach($plan as $key2 => $val){
                        if($key2 > 3){
                            if($val == 1){
                                if(in_array($titles[$key2],$planKeys)){
                                 $new_plans[] = $planValues[$titles[$key2]].'-'.$titles[$key2];
                                }
                            }
                        }
                    }
                    $max_plans=Array();
                   // print_r($new_plans);exit;
                    if(!empty($new_plans)){
                        $sourceModel = Mage::getModel('catalog/product')->getResource()
                        ->getAttribute('pay4leter_plans')->getSource();

                        $enable_sourceModel = Mage::getModel('catalog/product')->getResource()
                        ->getAttribute('pay4leter_enable')->getSource();
                       
                        $max_sourceModel = Mage::getModel('catalog/product')->getResource()
                        ->getAttribute('scrappage_sceme')->getSource();

                        $enable_arr=array('Yes');
                        $enable = array_map(array($enable_sourceModel, 'getOptionId'), $enable_arr);

                        $max_plans[] = $planValues['ONIF'.$plan[3]].'-ONIF'.$plan[3];
                        $maxValuesIds = array_map(array($max_sourceModel, 'getOptionId'), $max_plans);
                        print_r($new_plans);
                        print_r($maxValuesIds);
                        $valuesIds = array_map(array($sourceModel, 'getOptionId'), $new_plans);
                        //$product = Mage::getModel('catalog/product')->load($plan[0]);
                       
                        $product = Mage::getModel('catalog/product')->loadByAttribute('sku',$plan[1]);

                        if($product && $product->getId()){
                            $product->setData('pay4leter_plans', $valuesIds);
                            $product->setData('pay4leter_enable', $enable);
                             $product->setData('scrappage_sceme', $maxValuesIds);
                            $product->save();
                            $this->importInfo("Product id==>".$plan[1].' plans Saved');
                       
                        }
                        echo "Product id==>".$plan[1]."<br>";
                       
                       // echo "updated";exit;
                    }
                }
            }
         
      /*  else{
           
            $enable_arr=array('Yes');
             foreach($planData as $key=>$plan){
                //print_r($planValues);exit;
                if($key > 0){
                   // $new_plans = array()
                     $new_plans[] = $planValues['ONIF'.$plan[3]].'-ONIF'.$plan[3];
                  
                   
                   // print_r($sourceModel->getData());exit;
                        print_r($new_plans);
                        echo $plan[1];
                        $enable = array_map(array($enable_sourceModel, 'getOptionId'), $enable_arr);

                        $valuesIds = array_map(array($sourceModel, 'getOptionId'), $new_plans);
                       // print_r( $enable);exit;
                        //$product = Mage::getModel('catalog/product')->load($plan[0]);
                        $product = Mage::getModel('catalog/product')->loadByAttribute('sku',$plan[1]);
                       
                        if($product && $product->getId()){
                            $product->setData('pay4leter_enable', $enable);
                             $product->setData('scrappage_sceme', $valuesIds);
                            $product->save();
                            $this->importInfo("Product id==>".$plan[1].' plans Saved');
                       
                        }
                        exit;
                }
            }
        }*/
    }
    public function importInfo($log) {
        $this->log[] = "<li class='info'>" . $log . "</li>";

        Mage::log($log, Zend_Log::INFO);
    }

    public function getPay4leterTypeName()
    {
        
        $pay4laterArray = Mage::getStoreConfig('payment');
        $finalArr=array();
       // print_r($pay4laterArray);exit;
        $this->apiKey = $pay4laterArray['tab_required_settings']['pay4leter_api_key'];
        $this->redirectUrl = $pay4laterArray['tab_required_settings']['pay4leter_redirect_url'];
        $this->cancelUrl = $pay4laterArray['tab_required_settings']['pay4leter_cancel_url'];
       
        $this->isActive = $pay4laterArray['tab_required_settings']['active'];

        $this->loanvalue = $pay4laterArray['tab_global_configs']['loanvalue'];
    }
    public function getFirstPercentage($key){
       
       $fromDbModel = Mage::getModel('sp_pay4leter/pay4leter')->getCollection();
        $plancodes = $fromDbModel->addFieldToFilter('main_table.plan_name', array( $key))
                ->load();

        if(count($plancodes->getData()) <= 0){

            $value = Mage::getStoreConfig('payment/dynamic_plans');
            $value = Mage::helper('pay4leter/pay4laterplans')->_unserializeValue($value['dynamic_pay4later_plans']);
            $plan =explode("-",$key);
            foreach($value as $v){
                if($v['plan_code'] == $plan[1] ){
                    return $v['deposite_5'];
                    break;
                }
            }
        }
    }

    public function getPlanStack($plans){
        $value = Mage::getStoreConfig('payment/dynamic_plans');
        $globalPlans = Mage::helper('pay4leter/pay4laterplans')->_unserializeValue($value['dynamic_pay4later_plans']);
        $result = array();
        $planCodes = array();
        foreach($plans as $plan){
            $planCode = explode("-",$plan);
            $planCodes[] = $planCode[1];
        }
        foreach($globalPlans as $gp){
            if(in_array($gp['plan_code'],$planCodes)){
                $result[$gp['plan_code']] = $gp; 
            }
        }
        return $result;
    }
}
