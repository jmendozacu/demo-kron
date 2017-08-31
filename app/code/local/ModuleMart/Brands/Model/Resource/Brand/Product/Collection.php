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
class ModuleMart_Brands_Model_Resource_Brand_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection{
	/**
	 * remember if fields have been joined
	 * @var bool
	 */
	protected $_joinedFields = false;
	/**
	 * join the link table
	 * @access public
	 */
	public function joinFields(){
		if (!$this->_joinedFields){
			$this->getSelect()->join(
				array('related' => $this->getTable('brands/brand_product')),
				'related.product_id = e.entity_id',
				array('position')
			);
			$this->_joinedFields = true;
		}
		return $this;
	}
	/**
	 * add brand filter
	 * @access public
	 * @param ModuleMart_Brands_Model_Brand | int $brand
	 */
	public function addBrandFilter($brand){
		if ($brand instanceof ModuleMart_Brands_Model_Brand){
			$brand = $brand->getId();
		}
		if (!$this->_joinedFields){
			$this->joinFields();
		}
		$this->getSelect()->where('related.brand_id = ?', $brand);
		return $this;
	}
}