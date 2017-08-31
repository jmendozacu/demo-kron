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
 * Madapter Edit Form Content Tab Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @author  	Magestore Developer
 */
class Magestore_Madapter_Block_Adminhtml_Pem_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * prepare tab form's information
	 *
	 * @return Magestore_Madapter_Block_Adminhtml_Madapter_Edit_Tab_Form
	 */
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		$data['filename'] = Mage::helper('madapter')->getConfigNotice('name');
		
		$fieldset = $form->addFieldset('pem_form', array('legend'=>Mage::helper('madapter')->__('Upload PEM')));

		$fieldset->addField('filename', 'file', array(
			'label'		=> Mage::helper('madapter')->__('File'),
			'required'	=> false,
			'name'		=> 'filename',
		));
		
		$form->setValues($data);
		return parent::_prepareForm();
	}
}