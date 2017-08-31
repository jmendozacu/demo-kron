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
class ModuleMart_Brands_Model_Adminhtml_Observer{
	/**
	 * check if tab can be added
	 * @access protected
	 * @param Mage_Catalog_Model_Product $product
	 */
	protected function _canAddTab($product){
		if ($product->getId()){
			return true;
		}
		if (!$product->getAttributeSetId()){
			return false;
		}
		$request = Mage::app()->getRequest();
		if ($request->getParam('type') == 'configurable'){
			if ($request->getParam('attribtues')){
				return true;
			}
		}
		return false;
	}
	/**
	 * add the brand tab to products
	 * @access public
	 * @param Varien_Event_Observer $observer
	 */
	public function addBrandBlock($observer){
		$block = $observer->getEvent()->getBlock();
		$product = Mage::registry('product');
		if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs && $this->_canAddTab($product)){
			$block->addTab('brands', array(
				'label' => Mage::helper('brands')->__('Brands'),
				'url'   => Mage::helper('adminhtml')->getUrl('adminhtml/brands_brand_catalog_product/brands', array('_current' => true)),
				'class' => 'ajax',
			));
		}
		return $this;
	}
	/**
	 * save brand - product relation
	 * @access public
	 * @param Varien_Event_Observer $observer
	 */
	public function saveBrandData($observer){
		$post = Mage::app()->getRequest()->getPost('brands', -1);
		if ($post != '-1') {
			$post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
			$product = Mage::registry('product');
			$brandProduct = Mage::getResourceSingleton('brands/brand_product')->saveProductRelation($product, $post);
		}
		return $this;
	}}