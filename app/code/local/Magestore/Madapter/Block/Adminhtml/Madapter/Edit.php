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
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Madapter Edit Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @author  	Magestore Developer
 */
class Magestore_Madapter_Block_Adminhtml_Madapter_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'madapter';
		$this->_controller = 'adminhtml_madapter';
		
		$this->_updateButton('save', 'label', Mage::helper('madapter')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('madapter')->__('Delete Item'));
		
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('madapter_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'madapter_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'madapter_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}
	
	/**
	 * get text to show in header when edit an item
	 *
	 * @return string
	 */
	public function getHeaderText(){
		if(Mage::registry('madapter_data') && Mage::registry('madapter_data')->getId())
			return Mage::helper('madapter')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('madapter_data')->getTitle()));
		return Mage::helper('madapter')->__('Add Item');
	}
}