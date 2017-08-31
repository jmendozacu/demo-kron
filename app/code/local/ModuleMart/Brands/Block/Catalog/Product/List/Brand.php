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
class ModuleMart_Brands_Block_Catalog_Product_List_Brand extends Mage_Catalog_Block_Product_Abstract{
	/**
	 * get the list of brands
	 * @access protected
	 */
	public function getBrandCollection(){
		if (!$this->hasData('brand_collection')){
			$product = Mage::registry('product');
			$collection = Mage::getResourceSingleton('brands/brand_collection')
				->addStoreFilter(Mage::app()->getStore())

				->addFilter('status', 1)
				->addProductFilter($product);
			$collection->getSelect()->order('related_product.position', 'ASC');
			$this->setData('brand_collection', $collection);
		}
		return $this->getData('brand_collection');
	}
	
	/**
	 * enabled/disable brand logo on product view page
	 * @access public
	 */
	public function getBrandLogoEnabled(){
		return Mage::getStoreConfig('brands/brand/is_brand_enabled_product_page');
	}
}