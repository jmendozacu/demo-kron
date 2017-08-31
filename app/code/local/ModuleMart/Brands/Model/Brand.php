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
class ModuleMart_Brands_Model_Brand extends Mage_Core_Model_Abstract{
	/**
	 * Entity code.
	 * Can be used as part of method name for entity processing
	 */
	const ENTITY= 'brands_brand';
	const CACHE_TAG = 'brands_brand';
	/**
	 * Prefix of model events names
	 * @var string
	 */
	protected $_eventPrefix = 'brands_brand';
	
	/**
	 * Parameter name in event
	 * @var string
	 */
	protected $_eventObject = 'brand';
	protected $_productInstance = null;
	/**
	 * constructor
	 * @access public
	 */
	public function _construct(){
		parent::_construct();
		$this->_init('brands/brand');
	}
	/**
	 * before save brand
	 * @access protected
	 */
	protected function _beforeSave(){
		parent::_beforeSave();
		$now = Mage::getSingleton('core/date')->gmtDate();
		if ($this->isObjectNew()){
			$this->setCreatedAt($now);
		}
		$this->setUpdatedAt($now);
		return $this;
	}
	/**
	 * get the url to the brand details page
	 * @access public
	 */
	public function getBrandUrl(){
		if ($this->getUrlKey()){
			return Mage::getUrl('', array('_direct'=>$this->getUrlKey()));
		}
		return Mage::getUrl('brands/brand/view', array('id'=>$this->getId()));
	}
	/**
	 * check URL key
	 * @access public
	 * @param string $urlKey
	 * @param bool $active
	 */
	public function checkUrlKey($urlKey, $active = true){
		return $this->_getResource()->checkUrlKey($urlKey, $active);
	}
	/**
	 * get the brand Details
	 * @access public
	 */
	public function getBrandDetails(){
		$brand_details = $this->getData('brand_details');
		$helper = Mage::helper('cms');
		$processor = $helper->getBlockTemplateProcessor();
		$html = $processor->filter($brand_details);
		return $html;
	}
	/**
	 * save brand relation
	 * @access public
	 */
	protected function _afterSave() {
		$this->getProductInstance()->saveBrandRelation($this);
		return parent::_afterSave();
	}
	/**
	 * get product relation model
	 * @access public
	 */
	public function getProductInstance(){
		if (!$this->_productInstance) {
			$this->_productInstance = Mage::getSingleton('brands/brand_product');
		}
		return $this->_productInstance;
	}
	/**
	 * get selected products array
	 * @access public
	 */
	public function getSelectedProducts(){
		if (!$this->hasSelectedProducts()) {
			$products = array();
			foreach ($this->getSelectedProductsCollection() as $product) {
				$products[] = $product;
			}
			$this->setSelectedProducts($products);
		}
		return $this->getData('selected_products');
	}
	/**
	 * Retrieve collection selected products
	 * @access public
	 */
	public function getSelectedProductsCollection(){
		$collection = $this->getProductInstance()->getProductCollection($this);
		return $collection;
	}
}