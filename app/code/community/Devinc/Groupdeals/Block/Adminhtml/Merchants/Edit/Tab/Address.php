<?php

class Devinc_Groupdeals_Block_Adminhtml_Merchants_Edit_Tab_Address extends Mage_Adminhtml_Block_Widget_Form
{	
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);	  
  	  
        $fieldset = $form->addFieldset('merchants_address', array('legend'=>Mage::helper('groupdeals')->__('Address')));
         
        //update/translate address data
  	    $storeId = $this->getRequest()->getParam('store', 0);
  	    $redeem = Mage::getModel('license/module')->getDecodeString(Mage::registry('merchant_data')->getRedeem(), $storeId);
  	    Mage::registry('merchant_data')->setRedeem($redeem);
  	        	   	  
  	    $fieldset->addField('address_1', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('1st Address'),
            'name'      => 'address_1',
  	        'style'     => 'width:594px;',
            'required'  => false,
        )); 
  	        
  	    $fieldset->addField('address_2', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('2nd Address'),
            'name'      => 'address_2',
  	        'style'     => 'width:594px;',
            'required'  => false,
        )); 
  	        
  	    $fieldset->addField('address_3', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('3rd Address'),
            'name'      => 'address_3',
  	        'style'     => 'width:594px;',
            'required'  => false,
        )); 
  	        
  	    $fieldset->addField('address_4', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('4th Address'),
            'name'      => 'address_4',
  	        'style'     => 'width:594px;',
            'required'  => false,
        )); 
  	        
  	    $fieldset->addField('address_5', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('5th Address'),
            'name'      => 'address_5',
  	        'style'     => 'width:594px;',
            'required'  => false,
        )); 
  	        
  	    $field = $fieldset->addField('redeem', 'text', array(
            'label'     => Mage::helper('groupdeals')->__('Redeem at'),
            'name'      => 'redeem',
            'class'     => 'required-entry',
            'note'      => 'The redeem message will appear if no address is present.',
  	        'style'     => 'width:594px;',
            'required'  => true,
        )); 
  	    $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_renderer_input'));
         
        //set default/session values
        if ( Mage::registry('merchant_data') ) {	  
  		    $data = Mage::registry('merchant_data')->getData();  
  		    //decode address field		    
  		    if (isset($data['address']) && $data['address']!='') {
  		    	$address = explode('_;_',$data['address']);
  		    	for ($i = 0; $i<count($address); $i++) {
  		    	    if ($address[$i]!='') {
  		    	  	    $data['address_'.($i+1)] = $address[$i];
  		    	    }
  		    	}
  		    }
  		      		  
            $form->setValues($data);
        }
                  
        return parent::_prepareForm();
    }
}