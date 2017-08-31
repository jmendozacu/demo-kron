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
class ModuleMart_Brands_Model_Brand_Product extends Mage_Core_Model_Abstract{
	/**
	 * Initialize resource
	 */
	protected function _construct(){
		$this->_init('brands/brand_product');
	}
	/**
	 * Save data for brand-product relation
	 * @access public
	 */
	public function saveBrandRelation($brand){
		$data = $brand->getProductsData();
		if (!is_null($data)) {
			$this->_getResource()->saveBrandRelation($brand, $data);
		}
		return $this;
	}
	/**
	 * get products for brand
	 * @access public
	 */
	public function getProductCollection($brand){
		$collection = Mage::getResourceModel('brands/brand_product_collection')
			->addBrandFilter($brand);
		return $collection;
	}
}