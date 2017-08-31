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
class ModuleMart_Brands_Block_Adminhtml_Brand_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{
	/**
	 * constructor
	 * @access public
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('brand_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('brands')->__('Brand Information'));
	}
	/**
	 * before render html
	 * @access protected
	 */
	protected function _beforeToHtml(){
		$this->addTab('form_brand', array(
			'label'		=> Mage::helper('brands')->__('Brand Information'),
			'title'		=> Mage::helper('brands')->__('Brand Information'),
			'content' 	=> $this->getLayout()->createBlock('brands/adminhtml_brand_edit_tab_form')->toHtml(),
		));
		$this->addTab('form_meta_brand', array(
			'label'		=> Mage::helper('brands')->__('Meta Information'),
			'title'		=> Mage::helper('brands')->__('Meta Information'),
			'content' 	=> $this->getLayout()->createBlock('brands/adminhtml_brand_edit_tab_meta')->toHtml(),
		));
		if (!Mage::app()->isSingleStoreMode()){
			$this->addTab('form_store_brand', array(
				'label'		=> Mage::helper('brands')->__('Store views'),
				'title'		=> Mage::helper('brands')->__('Store views'),
				'content' 	=> $this->getLayout()->createBlock('brands/adminhtml_brand_edit_tab_stores')->toHtml(),
			));
		}
		$this->addTab('products', array(
			'label' => Mage::helper('brands')->__('Brand Products'),
			'url'   => $this->getUrl('*/*/products', array('_current' => true)),
   			'class'	=> 'ajax'
		));
		return parent::_beforeToHtml();
	}
}