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
class ModuleMart_Brands_Model_Resource_Brand_Product extends Mage_Core_Model_Resource_Db_Abstract{
/**
	 * initialize resource model
	 * @access protected
	 * @return void
	 */
	protected function  _construct(){
		$this->_init('brands/brand_product', 'rel_id');
	}
	/**
	 * Save brand - product relations
	 * @access public
	 * @param ModuleMart_Brands_Model_Brand $brand
	 * @param array $data
	 */
	public function saveBrandRelation($brand, $data){
		if (!is_array($data)) {
			$data = array();
		}
		$deleteCondition = $this->_getWriteAdapter()->quoteInto('brand_id=?', $brand->getId());
		$this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

		foreach ($data as $productId => $info) {
			$this->_getWriteAdapter()->insert($this->getMainTable(), array(
				'brand_id'  	=> $brand->getId(),
				'product_id' 	=> $productId,
				'position'  	=> @$info['position']
			));
		}
		return $this;
	}
	/**
	 * Save  product - brand relations
	 * @access public
	 * @param Mage_Catalog_Model_Product $prooduct
	 * @param array $data
	 */
	public function saveProductRelation($product, $data){
		if (!is_array($data)) {
			$data = array();
		}
		$deleteCondition = $this->_getWriteAdapter()->quoteInto('product_id=?', $product->getId());
		$this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);
		
		foreach ($data as $brandId => $info) {
			$this->_getWriteAdapter()->insert($this->getMainTable(), array(
				'brand_id' => $brandId,
				'product_id' => $product->getId(),
				'position'   => @$info['position']
			));
		}
		return $this;
	}
}