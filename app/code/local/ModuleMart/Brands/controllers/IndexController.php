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
class ModuleMart_Brands_IndexController extends Mage_Core_Controller_Front_Action{
	/**
 	 * default action
 	 * @access public
 	 */
 	public function indexAction(){
		$this->loadLayout();
 		if (Mage::helper('brands/brand')->getUseBreadcrumbs()){
			if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')){
				$breadcrumbBlock->addCrumb('home', array(
							'label'	=> Mage::helper('brands')->__('Home'), 
							'link' 	=> Mage::getUrl(),
						)
				);
				$breadcrumbBlock->addCrumb('brands', array(
							'label'	=> Mage::helper('brands')->__('Brands'), 
							'link'	=> '',
					)
				);
			}
		}
		$headBlock = $this->getLayout()->getBlock('head');
		if ($headBlock) {
			$headBlock->setTitle(Mage::getStoreConfig('brands/brand/meta_title'));
			$headBlock->setKeywords(Mage::getStoreConfig('brands/brand/meta_keywords'));
			$headBlock->setDescription(Mage::getStoreConfig('brands/brand/meta_description'));
		}
		$this->renderLayout();
	}
	/**
 	 * view brand action
 	 * @access public
 	 */
	public function viewAction(){
		$brandId 	= $this->getRequest()->getParam('id', 0);
		$brand 	= Mage::getModel('brands/brand')
						->setStoreId(Mage::app()->getStore()->getId())
						->load($brandId);
		if (!$brand->getId()){
			$this->_forward('no-route');
		}
		elseif (!$brand->getStatus()){
			$this->_forward('no-route');
		}
		else{
			Mage::register('current_brands', $brand);
			$this->loadLayout();
			if ($root = $this->getLayout()->getBlock('root')) {
				$root->addBodyClass('brands-brand brands-brand' . $brand->getId());
			}
			if (Mage::helper('brands/brand')->getUseBreadcrumbs()){
				if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')){
					$breadcrumbBlock->addCrumb('home', array(
								'label'	=> Mage::helper('brands')->__('Home'), 
								'link' 	=> Mage::getUrl(),
							)
					);
					$breadcrumbBlock->addCrumb('brands', array(
								'label'	=> Mage::helper('brands')->__('Brands'), 
								'link'	=> Mage::helper('brands')->getBrandsUrl(),
						)
					);
					$breadcrumbBlock->addCrumb('brand', array(
								'label'	=> $brand->getBrandName(), 
								'link'	=> '',
						)
					);
				}
			}
			$headBlock = $this->getLayout()->getBlock('head');
			if ($headBlock) {
				if ($brand->getMetaTitle()){
					$headBlock->setTitle($brand->getMetaTitle());
				}
				else{
					$headBlock->setTitle($brand->getBrandName());
				}
				$headBlock->setKeywords($brand->getMetaKeywords());
				$headBlock->setDescription($brand->getMetaDescription());
			}
			$this->renderLayout();
		}
	}
}