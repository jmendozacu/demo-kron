<?php

class Devinc_Groupdeals_Block_Adminhtml_Merchants_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{	
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);	  
    	  
        $fieldset = $form->addFieldset('merchants_general', array('legend'=>Mage::helper('groupdeals')->__('General')));           	 	        
        //update/translate merchant data
        $storeId = $this->getRequest()->getParam('store', 0);
        $data = Mage::registry('merchant_data')->getData();
    	$data['name'] = Mage::getModel('license/module')->getDecodeString(Mage::registry('merchant_data')->getName(), $storeId);
    	$data['description'] = Mage::getModel('license/module')->getDecodeString(Mage::registry('merchant_data')->getDescription(), $storeId);
    	$data['website'] = Mage::getModel('license/module')->getDecodeString(Mage::registry('merchant_data')->getWebsite(), $storeId);
    	$data['email'] = Mage::getModel('license/module')->getDecodeString(Mage::registry('merchant_data')->getEmail(), $storeId);
    	$data['facebook'] = Mage::getModel('license/module')->getDecodeString(Mage::registry('merchant_data')->getFacebook(), $storeId);
    	$data['twitter'] = Mage::getModel('license/module')->getDecodeString(Mage::registry('merchant_data')->getTwitter(), $storeId);
    	$data['phone'] = Mage::getModel('license/module')->getDecodeString(Mage::registry('merchant_data')->getPhone(), $storeId);
    	$data['mobile'] = Mage::getModel('license/module')->getDecodeString(Mage::registry('merchant_data')->getMobile(), $storeId);
    	$data['business_hours'] = Mage::getModel('license/module')->getDecodeString(Mage::registry('merchant_data')->getBusinessHours(), $storeId);	  
    	Mage::registry('merchant_data')->setData($data);	  
    	  
    	//add fields
    	$field = $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Name'),
            'name'      => 'name',
            'class'     => 'required-entry',
    		'style'     => 'width:594px;',
            'required'  => true,
        ));	  
    	$field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
    	  
    	$fieldset->addField('merchant_logo', 'image', array(
            'name'      => 'merchant_logo',
            'label'     => Mage::helper('groupdeals')->__('Logo'),
            'class'     => '',
            'required'  => false,
        ));
    	  
    	$field = $fieldset->addField('description', 'editor', array(
            'label'     => Mage::helper('groupdeals')->__('Description'),
            'name'      => 'description',
            'class'     => 'required-entry',
    		'wysiwyg'   => true,
    		'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
    		'theme'     => 'simple',
    		'style'     => 'width:594px; height:250px;',
            'required'  => true,
        ));	 	  
    	$field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
    	  	  
    	$field = $fieldset->addField('website', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Website'),
            'name'      => 'website',
          	'style'     => 'width:594px;',
        ));	
        $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
        	  
        $field = $fieldset->addField('email', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Email'),
            'name'      => 'email',
            'class'     => 'required-entry',
            'style'     => 'width:594px;',
            'required'  => true,
        ));		  
        $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
        
        $field = $fieldset->addField('facebook', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Facebook link'),
            'name'      => 'facebook',
          	'style'     => 'width:594px;',
        ));		  
        $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
        
        $field = $fieldset->addField('twitter', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Twitter link'),
            'name'      => 'twitter',
          	'style'     => 'width:594px;',
        ));		  
        $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
        	  
        $field = $fieldset->addField('phone', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Phone'),
            'name'      => 'phone',
          	'style'     => 'width:594px;',
        ));		  
        $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
        	  
        $field = $fieldset->addField('mobile', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Mobile'),
            'name'      => 'mobile',
          	'style'     => 'width:594px;',
        ));		  
        $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
        	  
        $field = $fieldset->addField('business_hours', 'textarea', array(
            'label'     => Mage::helper('groupdeals')->__('Business Hours'),
            'name'      => 'business_hours',
          	'style'     => 'width:594px;',
        ));	
        $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
          
          
        if(!Mage::getModel('groupdeals/merchants')->isMerchant()){		
            $fieldset->addField('status', 'select', array(
          	  'label'     => Mage::helper('groupdeals')->__('Status'),
          	  'name'      => 'status',
          	  'class'     => 'required-entry validate-select',
          	  'required'  => true,
          	  'values'    => array(
          		  array(
          			  'value'     => 1,
          			  'label'     => Mage::helper('groupdeals')->__('Enabled'),
          		  ),
    
          		  array(
          			  'value'     => 2,
          			  'label'     => Mage::helper('groupdeals')->__('Disabled'),
          		  ),
          		  
          		  array(
          			  'value'     => 3,
          			  'label'     => Mage::helper('groupdeals')->__('Pending Approval'),
          		  ),
          	  ),
            ));
        }
       
        //set default/session values
        if ($data = Mage::registry('merchant_data')) {	
            $form->setValues($data);
        }
        
        return parent::_prepareForm();
    }
}