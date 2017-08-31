<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::helper('catalog')->isModuleEnabled('Mage_Cms')) {
            if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
                $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
            }
        }
    }
  	
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('product'));
        $group = $this->getGroup();
  	  
        $fieldset = $form->addFieldset('group_fields'.$group->getId(), array('legend'=>Mage::helper('groupdeals')->__('General'), 'class'=>'fieldset-medium'));
             
        //load data
        $storeId = $this->getRequest()->getParam('store', 0);
	    if ($this->getRequest()->getParam('type', false)) {
	    	$productType = $this->getRequest()->getParam('type', false);
	    } else {
	    	$productType = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id'))->getTypeId();
	    }	    
	    
        //add name field
        $nameAttribute = $this->getNameAttribute();
        $this->_setFieldset($nameAttribute, $fieldset);
  	 			
  	 	//add merchant dropdown	    	  	
  	  	if ($merchant = Mage::getModel('groupdeals/merchants')->isMerchant()) {		
  		  	$merchants[] = array(
  			  	'label' => Mage::getModel('license/module')->getDecodeString($merchant->getName(), $storeId),
  			  	'value' => $merchant->getId()
  		  	);
  	 	} else {
  	  		$merchants[] = array(
  				'label' => '--- No Merchant ---',
  				'value' => 0
  	  		);   
  		  	$merchantCollection = Mage::getModel('groupdeals/merchants')->getCollection()->addFieldToFilter('status', array('eq' => 1));
  			
  		  	foreach ($merchantCollection as $merchant) {
  				$merchants[] = array(
  					'label' => Mage::getModel('license/module')->getDecodeString($merchant->getName(), $storeId),
  					'value' => $merchant->getId()
  				);
  		  	}	  
  		  	sort($merchants);	  
  	  	}
  
  	    $field = $fieldset->addField('merchant_id', 'select', array(
              'label'     => Mage::helper('groupdeals')->__('Merchant'),
              'name'      => 'merchant_id',
              'class'     => 'validate-select required-entry',
              'required'  => true,
              'values'    => $merchants,
        ));	  
        $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_element'));
  	  
  	  	//add country dropdown
  	  	$countryCollection = Mage::getModel('directory/country_api')->items();
  	           
  	  	$countries[] = array(
            'label' => '--- Universal Deal ---',
            'value' => ''
        );   
  			
        foreach ($countryCollection as $country) {
            $countries[] = array(
                'label' => $country['name'],
                'value' => $country['country_id']
            );
        }		
  
  	    sort($countries);	  
  
  	    $field = $fieldset->addField('country_id', 'select', array(
            'label'     => Mage::helper('groupdeals')->__('Country'),
            'name'      => 'country_id',
  			'onchange'  => 'regionReload(this.value)',
            'required'  => false,
            'values'    => $countries,
        ));	  
        $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_element'));	  
  	  
  	    //add state/city fields
  	    $field = $fieldset->addField('region_city', 'text', array(
              'label'     => Mage::helper('groupdeals')->__('State/City'),
              'name'      => 'region_city',
              'required'  => false,
        ));	
        $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_regioncity'));	        
        	  
  	    //add date fields
   	    //setting the date format type for the date fields
   	    /*
if (substr(Mage::app()->getLocale()->getLocaleCode(),0,2)!='en') {
  	    	  $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
  	    			Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
  	    	  );
  	    } else {		
  	    	  $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
  	    			Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
  	    	  );
  	    }	
*/
  	    $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
  	    
  	    $field = $fieldset->addField('groupdeal_datetime_from', 'date', array(
            'name'      => 'product[groupdeal_datetime_from]',
            'time'      => true,
            'label'     => Mage::helper('groupdeals')->__('Date/Time From'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'class'     => 'required-entry',
            'required'  => true,
            'format'    => $dateFormatIso,
            'style'	    => 'width:162px !important',	
        ));
        $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_element'));	
  	
        $field = $fieldset->addField('groupdeal_datetime_to', 'date', array(
            'name'      => 'product[groupdeal_datetime_to]',
            'time'      => true,
            'label'     => Mage::helper('groupdeals')->__('Date/Time To'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'class'     => 'required-entry',
            'required'  => true,
            'format'    => $dateFormatIso,
            'style'	    => 'width:162px !important',	
        ));	
        $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_element'));	
        
        //add general group attributes
        $attributes = $this->getGeneralAttributes();
        $this->_setFieldset($attributes, $fieldset);
        
        /**
         * Add new attribute button if not image tab
         */
        if (Mage::getSingleton('admin/session')->isAllowed('catalog/attributes/attributes')) {
            $headerBar = $this->getLayout()->createBlock(
                'adminhtml/catalog_product_edit_tab_attributes_create'
            );
            
            $headerBar->getConfig()
                ->setTabId('group_' . $group->getId())
                ->setGroupId($group->getId())
                ->setStoreId($form->getDataObject()->getStoreId())
                ->setAttributeSetId($form->getDataObject()->getAttributeSetId())
                ->setTypeId($form->getDataObject()->getTypeId())
                ->setProductId($form->getDataObject()->getId());

            $fieldset->setHeaderBar(
                $headerBar->toHtml()
            );
        }
        
        //set attribute renderers + add sufix
        $suffix = 'product';
        $element = $form->getElement('name');
        $element->setRenderer($this->getLayout()->createBlock('adminhtml/catalog_form_renderer_fieldset_element'));
        if ($name = $element->getName()) {
            $element->setName($form->addSuffixToName($name, $suffix));
        }
            
        foreach ($attributes as $attribute) {
            $element = $form->getElement($attribute->getAttributeCode());
            $element->setRenderer($this->getLayout()->createBlock('adminhtml/catalog_form_renderer_fieldset_element'));
            if ($name = $element->getName()) {
                $element->setName($form->addSuffixToName($name, $suffix));
            }
        }
        
        if ($productType=='bundle') {
        	if ($weight = $form->getElement('weight')) {
        	    $weight->setRenderer(
        	        $this->getLayout()->createBlock('bundle/adminhtml_catalog_product_edit_tab_attributes_extend')
        	            ->setDisableChild(true)
        	    );
        	}
        	
        	if ($sku = $form->getElement('sku')) {
        	    $sku->setRenderer(
        	        $this->getLayout()->createBlock('bundle/adminhtml_catalog_product_edit_tab_attributes_extend')
        	            ->setDisableChild(false)
        	    );
        	}
        }
        
        if ($urlKey = $form->getElement('url_key')) {
            $urlKey->setRenderer(
                $this->getLayout()->createBlock('adminhtml/catalog_form_renderer_attribute_urlkey')
            );
        }
  
        if (Mage::registry('product')->hasLockedAttributes()) {
            foreach (Mage::registry('product')->getLockedAttributes() as $attribute) {
                if ($element = $form->getElement($attribute)) {
                    $element->setReadonly(true, true);
                }
            }
        }
                   
        //add target_met_email dropdown
        if ($productType=='virtual') {
        	$field = $fieldset->addField('target_met_email', 'select', array(
  	  		    'label'     => Mage::helper('groupdeals')->__('Send Coupons to Invoiced Orders after the Target has been met'),
  	  		    'name'      => 'target_met_email',
  	  		    'class'     => 'validate-select',
  	  		    'required'  => true,
  	  		    'values'    => array(
  	  		    	array(
  	  		    		'value'     => 0,
  	  		    		'label'     => Mage::helper('groupdeals')->__('No'),
  	  		    	),
  	  		        array(
  	  		    		'value'     => 1,
  	  		    		'label'     => Mage::helper('groupdeals')->__('Yes'),
  	  		    	),
  	  		    ),
  	  		));  	  	
        	$field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_element'));	  	
        } 	          
        
  	    //add position field
  	    $field = $fieldset->addField('position', 'text', array(
              'label'     => Mage::helper('groupdeals')->__('Position'),
              'name'      => 'position',
              'required'  => false,
        ));	
        $field->setRenderer($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_renderer_element'));	       
                
        /**
         * Set attribute default values for new product
         */
        $values = Mage::registry('product')->getData();
        if (!Mage::registry('product')->getId()) {
            foreach ($attributes as $attribute) {
                if (!isset($values[$attribute->getAttributeCode()])) {
                    $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                }
            }
        }
        
        //set country_id value
        if ($groupdealId = $this->getRequest()->getParam('groupdeals_id', false)) {
	        $countryId = Mage::getModel('groupdeals/crc')->getDealCountryId($groupdealId);
	        $values['country_id'] = $countryId;	        
        }
        
        //set default/session values
        if (Mage::registry('groupdeals_data')) {
  		    $data = array_merge(Mage::registry('groupdeals_data')->getData(), $values);			
            $form->setValues($data);
        }
        
        $this->setForm($form);	  
        
        return parent::_prepareForm();
    }

    protected function _getAdditionalElementTypes()
    {
        $result = array(
            'price'    => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_price'),
            'weight'   => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_weight'),
            'gallery'  => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_gallery'),
            'image'    => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_image'),
            'boolean'  => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_boolean'),
            'textarea' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_helper_form_wysiwyg')
        );

        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_catalog_product_edit_element_types', array('response'=>$response));

        foreach ($response->getTypes() as $typeName=>$typeClass) {
            $result[$typeName] = $typeClass;
        }

        return $result;
    }
}