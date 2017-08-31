<?php
 /**
 * ModuleMart_Brands extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Module-Mart License
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.modulemart.com/license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to modules@modulemart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.modulemart.com for more information.
 *
 * @category   ModuleMart
 * @package    ModuleMart_Brands
 * @author-email  modules@modulemart.com
 * @copyright  Copyright 2014 © modulemart.com. All Rights Reserved
 */
class ModuleMart_Brands_Block_Adminhtml_Brand_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form{	
	/**
	 * prepare the form
	 * @access protected
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$form->setHtmlIdPrefix('brand_');
		$form->setFieldNameSuffix('brand');
		$this->setForm($form);
		$fieldset = $form->addFieldset('brand_form', array('legend'=>Mage::helper('brands')->__('Brand')));
		$fieldset->addType('file', Mage::getConfig()->getBlockClassName('brands/adminhtml_brand_helper_file'));
		$wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();

		$fieldset->addField('brand_name', 'text', array(
			'label' => Mage::helper('brands')->__('Brand Name'),
			'name'  => 'brand_name',
			'required'  => true,
			'class' => 'required-entry',

		));

		$fieldset->addField('brand_logo', 'file', array(
			'label' => Mage::helper('brands')->__('Brand Logo'),
			'name'  => 'brand_logo',

		));

		$fieldset->addField('featured_brand', 'select', array(
			'label' => Mage::helper('brands')->__('Featured'),
			'name'  => 'featured_brand',
			'required'  => false,
			'note'	=> $this->__('Featured brands will be shown in slider.'),

			'values'=> array(
				array(
					'value' => 1,
					'label' => Mage::helper('brands')->__('Yes'),
				),
				array(
					'value' => 0,
					'label' => Mage::helper('brands')->__('No'),
				),
			),
		));

		$fieldset->addField('url_key', 'text', array(
			'label' => Mage::helper('brands')->__('Url key'),
			'name'  => 'url_key',
			'required'  => true,
			'class' => 'required-entry',
			'note'	=> Mage::helper('brands')->__('Relative to Website Base URL')
		));
		
		$fieldset->addField('status', 'select', array(
			'label' => Mage::helper('brands')->__('Status'),
			'name'  => 'status',
			'values'=> array(
				array(
					'value' => 1,
					'label' => Mage::helper('brands')->__('Enabled'),
				),
				array(
					'value' => 0,
					'label' => Mage::helper('brands')->__('Disabled'),
				),
			),
		));
		
		$fieldset->addField('brand_details', 'editor', array(
			'label' => Mage::helper('brands')->__('Details'),
			'name'  => 'brand_details',
			'config'	=> $wysiwygConfig,
			'note'	=> $this->__('Brand Details'),
			'required'  => true,
			'class' => 'required-entry',

		));
		
		$fieldset->addField('is_on_top', 'select', array(
			'label' => Mage::helper('brands')->__('Include in Navigation Menu'),
			'name'  => 'is_on_top',
			'required'  => false,
			'note'	=> $this->__('Include in Navigation Menu.'),

			'values'=> array(
				array(
					'value' => 1,
					'label' => Mage::helper('brands')->__('Yes'),
				),
				array(
					'value' => 0,
					'label' => Mage::helper('brands')->__('No'),
				),
			),
		));
		
		if (Mage::app()->isSingleStoreMode()){
			$fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('current_brand')->setStoreId(Mage::app()->getStore(true)->getId());
		}
		if (Mage::getSingleton('adminhtml/session')->getBrandData()){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getBrandData());
			Mage::getSingleton('adminhtml/session')->setBrandData(null);
		}
		elseif (Mage::registry('current_brand')){
			$form->setValues(Mage::registry('current_brand')->getData());
		}
		return parent::_prepareForm();
	}
}