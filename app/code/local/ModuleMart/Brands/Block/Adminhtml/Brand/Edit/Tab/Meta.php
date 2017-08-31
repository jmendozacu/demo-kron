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
class ModuleMart_Brands_Block_Adminhtml_Brand_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form{
	/**
	 * prepare the form
	 * @access protected
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$form->setFieldNameSuffix('brand');
		$this->setForm($form);
		$fieldset = $form->addFieldset('brand_meta_form', array('legend'=>Mage::helper('brands')->__('Meta information')));
		$fieldset->addField('meta_title', 'text', array(
			'label' => Mage::helper('brands')->__('Meta-title'),
			'name'  => 'meta_title',
		));
		$fieldset->addField('meta_description', 'textarea', array(
			'name'  	=> 'meta_description',
			'label' 	=> Mage::helper('brands')->__('Meta-description'),
  		));
  		$fieldset->addField('meta_keywords', 'textarea', array(
			'name'  	=> 'meta_keywords',
			'label' 	=> Mage::helper('brands')->__('Meta-keywords'),
  		));
  		$form->addValues(Mage::registry('current_brand')->getData());
		return parent::_prepareForm();
	}
}