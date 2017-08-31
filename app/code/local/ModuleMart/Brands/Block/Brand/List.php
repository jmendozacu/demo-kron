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
class ModuleMart_Brands_Block_Brand_List extends Mage_Core_Block_Template{
	/**
	 * initialize
	 * @access public
	 */
 	public function __construct(){
		parent::__construct();
 		$brands = Mage::getResourceModel('brands/brand_collection')
 						->addStoreFilter(Mage::app()->getStore())
				->addFilter('status', 1)
		;
		$brands->setOrder('brand_name', 'asc');
		$this->setBrands($brands);
		
		/// brands slider
		$slider = Mage::getResourceModel('brands/brand_collection')
 						->addStoreFilter(Mage::app()->getStore())
				->addFilter('status', 1)
				->addFilter('featured_brand', 1)
		;
		$slider->setOrder('brand_name', 'asc');
		$this->setBrandsSlider($slider);
		
		/// brands sidebar
		$sideBlock = Mage::getStoreConfig('brands/left_block/total_brands_sidebar');
		$brands_sidebar = Mage::getResourceModel('brands/brand_collection')
 						->addStoreFilter(Mage::app()->getStore())
				->addFilter('status', 1)
		;
		$brands_sidebar->setPageSize($sideBlock);
		$brands_sidebar->setOrder('brand_name', 'asc');
		$this->setBrandsLeftBlock($brands_sidebar);
		
		///left block
		$this->setBrandsSidebarTitle(Mage::getStoreConfig('brands/left_block/sidebar_title'));
		/// left block brand images
		$this->setEnableBrandImages(Mage::getStoreConfig('brands/left_block/show_images'));
		/// enable slider
		$this->setEnableBrandSlider(Mage::getStoreConfigFlag('brands/brands_slider/is_enabled'));
		/// autoplay slider
		$this->setFeaturedTitle(Mage::getStoreConfig('brands/brands_slider/featured_brands_title'));
		/// autoplay speed
		$this->setShowTotalItems(Mage::getStoreConfig('brands/brands_slider/show_items'));
		/// animation speed
		$this->setAutoPlay(Mage::getStoreConfig('brands/brands_slider/is_auto_play'));
		//jQuery Is Enabled
		$this->setIsjQueryEnabled(Mage::getStoreConfigFlag('brands/brand/jquery_enabled'));
	}	
	/**
	 * prepare the layout
	 * @access protected
	 */
	protected function _prepareLayout(){
		parent::_prepareLayout();
		$pager = $this->getLayout()->createBlock('page/html_pager', 'brands.brand.html.pager')
			->setCollection($this->getBrands());
		$this->setChild('pager', $pager);
		$this->getBrands()->load();
		return $this;
	}
	/**
	 * get the pager html
	 * @access public
	 */
	public function getPagerHtml(){
		return $this->getChildHtml('pager');
	}
	/**
	 * @access public
	 */
	public function getBrandsPageUrl() {
		return Mage::getBaseUrl().'brands/';
	}
}