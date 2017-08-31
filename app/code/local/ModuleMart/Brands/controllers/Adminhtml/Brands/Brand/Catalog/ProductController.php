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
require_once ("Mage/Adminhtml/controllers/Catalog/ProductController.php");
class ModuleMart_Brands_Adminhtml_Brands_Brand_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController{
	/**
	 * construct
	 * @access protected
	 */
	protected function _construct(){
		// Define module dependent translate
		$this->setUsedModuleName('ModuleMart_Brands');
	}
	/**
	 * brands in the catalog page
	 * @access public
	 */
	public function brandsAction(){
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('product.edit.tab.brand')
			->setProductBrands($this->getRequest()->getPost('product_brands', null));
		$this->renderLayout();
	}
	/**
	 * brands grid in the catalog page
	 * @access public
	 */
	public function brandsGridAction(){
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('product.edit.tab.brand')
			->setProductBrands($this->getRequest()->getPost('product_brands', null));
		$this->renderLayout();
	}
}