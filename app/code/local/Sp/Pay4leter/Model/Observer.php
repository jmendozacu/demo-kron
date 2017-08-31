<?php

class Sp_Pay4leter_Model_Observer
{
    public function importPay4leterData($observer)
    {
    	//var_dump($_FILES);exit;
        if($_FILES && isset($_FILES['groups']['tmp_name']['import_pay4leter'])){
            $helper = Mage::helper('pay4leter');
            $event = $observer->getEvent();
            $path = Mage::getBaseDir() . DS . 'var' . DS . 'uploads';
            $file_path = $_FILES['groups']['tmp_name']['import_pay4leter']['fields']['upload_file']['value']; 
            if($file_path!=='' && !empty($file_path)){
                $filedata=$helper->addPlanData($file_path);
                //$planTypes= $filedata[0];
            }
        }
    }

     public function addInterviewsExportField($observer)
    {
        //echo "echo here comesd";exit
        $planValues = Mage::helper('pay4leter/pay4laterplans')->getConfigValue();

        $attribute_model = Mage::getModel('eav/entity_attribute');
        $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;

        $attribute_code = $attribute_model->getIdByCode('catalog_product', 'pay4leter_plans');

        $attribute = $attribute_model->load($attribute_code);
        
        $attribute_table = $attribute_options_model->setAttribute($attribute);
        $options = $attribute_options_model->getAllOptions(false);

        $new_options=array();
        $optionLabels=array();
        foreach($options as $option){
           // print_r($option);
            $label = explode("-",$option['label']);
          //  print_r($label);
            $tmp_label = (count($label)>1)?$label[1]:$label[0];
            $optionLabels[]=$tmp_label;
            if(!in_array($tmp_label,array_keys($planValues))){
                $new_options['delete'][$option['value']] = true;
                $new_options['value'][$option['value']] = true;
            }
        }

        $new_options1['attribute_id'] = $attribute->getAttributeId();
        foreach($planValues as $key=>$plan){
            if(!in_array($key,$optionLabels)){
                $new_options1['values'][] = $plan.'-'.$key;
            }
        } 

        $setup = new Mage_Eav_Model_Entity_Setup('core_setup');

        $setup->addAttributeOption($new_options);
        $setup->addAttributeOption($new_options1);

        // for scrappage drop down options

        $new_options=array();
        $optionLabels=array();

         $scrapp_attribute = Mage::getModel('catalog/resource_eav_attribute')
        ->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'scrappage_sceme');

        $scrapp_options   = $scrapp_attribute->getSource()->getAllOptions();
         foreach($scrapp_options as $option){
            
            $label = explode("-",$option['label']);
            $tmp_label = (count($label)>1)?$label[1]:'';
            $optionLabels[]=$tmp_label;
            if(!in_array($tmp_label,array_keys($planValues))){
                $new_options['delete'][$option['value']] = true;
                $new_options['value'][$option['value']] = true;
            }
        }
        $new_options2['attribute_id'] = $scrapp_attribute->getAttributeId();
        foreach($planValues as $key=>$plan){
            if(!in_array($key,$optionLabels)){
                $new_options2['values'][] = $plan.'-'.$key;
            }
        }
        $setup1 = new Mage_Eav_Model_Entity_Setup('core_setup'); 
        
        $setup1->addAttributeOption($new_options);
        $setup1->addAttributeOption($new_options2);   
    
    }
}