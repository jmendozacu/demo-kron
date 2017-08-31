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
class ModuleMart_Brands_Block_Adminhtml_Brand_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{
	/**
	 * constuctor
	 * @access public
	 */
	public function __construct(){
		parent::__construct();
		$this->_blockGroup = 'brands';
		$this->_controller = 'adminhtml_brand';
		$this->_updateButton('save', 'label', Mage::helper('brands')->__('Save Brand'));
		$this->_updateButton('delete', 'label', Mage::helper('brands')->__('Delete Brand'));
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('brands')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);
		$this->_formScripts[] = "
			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}
	/**
	 * get the edit form header
	 * @access public
	 */
	public function getHeaderText(){
		if( Mage::registry('brand_data') && Mage::registry('brand_data')->getId() ) {
			return Mage::helper('brands')->__("Edit Brand '%s'", $this->htmlEscape(Mage::registry('brand_data')->getBrandName()));
		} 
		else {
			return Mage::helper('brands')->__('Add Brand');
		}
	}
}