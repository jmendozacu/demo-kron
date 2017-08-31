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
class ModuleMart_Brands_Adminhtml_Brands_BrandController extends ModuleMart_Brands_Controller_Adminhtml_Brands{
	/**
	 * init the brand
	 * @access protected
	 */
	protected function _initBrand(){
		$brandId  = (int) $this->getRequest()->getParam('id');
		$brand	= Mage::getModel('brands/brand');
		if ($brandId) {
			$brand->load($brandId);
		}
		Mage::register('current_brand', $brand);
		return $brand;
	}
 	/**
	 * default action
	 * @access public
	 */
	public function indexAction() {
		$this->loadLayout();
		$this->_title(Mage::helper('brands')->__('Brands'))
			 ->_title(Mage::helper('brands')->__('Brands'));
		$this->renderLayout();
	}
	/**
	 * grid action
	 * @access public
	 */
	public function gridAction() {
		$this->loadLayout()->renderLayout();
	}
	/**
	 * edit brand - action
	 * @access public
	 */
	public function editAction() {
		$brandId	= $this->getRequest()->getParam('id');
		$brand  	= $this->_initBrand();
		if ($brandId && !$brand->getId()) {
			$this->_getSession()->addError(Mage::helper('brands')->__('This brand no longer exists.'));
			$this->_redirect('*/*/');
			return;
		}
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		if (!empty($data)) {
			$brand->setData($data);
		}
		Mage::register('brand_data', $brand);
		$this->loadLayout();
		$this->_title(Mage::helper('brands')->__('Brands'))
			 ->_title(Mage::helper('brands')->__('Brands'));
		if ($brand->getId()){
			$this->_title($brand->getBrandName());
		}
		else{
			$this->_title(Mage::helper('brands')->__('Add brand'));
		}
		if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) { 
			$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true); 
		}
		$this->renderLayout();
	}
	/**
	 * new brand action
	 * @access public
	 */
	public function newAction() {
		$this->_forward('edit');
	}
	/**
	 * save brand - action
	 * @access public
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost('brand')) {
			try {
				$brand = $this->_initBrand();
				$brand->addData($data);
				$brand_logoName = $this->_uploadAndGetName('brand_logo', Mage::helper('brands/brand')->getFileBaseDir(), $data);
				$brand->setData('brand_logo', $brand_logoName);
				$products = $this->getRequest()->getPost('products', -1);
				if ($products != -1) {
					$brand->setProductsData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($products));
				}
				$brand->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brands')->__('Brand was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $brand->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} 
			catch (Mage_Core_Exception $e){
				if (isset($data['brand_logo']['value'])){
					$data['brand_logo'] = $data['brand_logo']['value'];
				}
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
			catch (Exception $e) {
				Mage::logException($e);
				if (isset($data['brand_logo']['value'])){
					$data['brand_logo'] = $data['brand_logo']['value'];
				}
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brands')->__('There was a problem saving the brand.'));
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brands')->__('Unable to find brand to save.'));
		$this->_redirect('*/*/');
	}
	/**
	 * delete brand - action
	 * @access public
	 */
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0) {
			try {
				$brand = Mage::getModel('brands/brand');
				$brand->setId($this->getRequest()->getParam('id'))->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brands')->__('Brand was successfully deleted.'));
				$this->_redirect('*/*/');
				return; 
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brands')->__('There was an error deleteing brand.'));
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				Mage::logException($e);
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brands')->__('Could not find brand to delete.'));
		$this->_redirect('*/*/');
	}
	/**
	 * mass delete brand - action
	 * @access public
	 */
	public function massDeleteAction() {
		$brandIds = $this->getRequest()->getParam('brand');
		if(!is_array($brandIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brands')->__('Please select brands to delete.'));
		}
		else {
			try {
				foreach ($brandIds as $brandId) {
					$brand = Mage::getModel('brands/brand');
					$brand->setId($brandId)->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('brands')->__('Total of %d brands were successfully deleted.', count($brandIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brands')->__('There was an error deleteing brands.'));
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/index');
	}
	/**
	 * mass status change - action
	 * @access public
	 */
	public function massStatusAction(){
		$brandIds = $this->getRequest()->getParam('brand');
		if(!is_array($brandIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brands')->__('Please select brands.'));
		} 
		else {
			try {
				foreach ($brandIds as $brandId) {
				$brand = Mage::getSingleton('brands/brand')->load($brandId)
							->setStatus($this->getRequest()->getParam('status'))
							->setIsMassupdate(true)
							->save();
				}
				$this->_getSession()->addSuccess($this->__('Total of %d brands were successfully updated.', count($brandIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brands')->__('There was an error updating brands.'));
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/index');
	}
	/**
	 * mass Featured change - action
	 * @access public
	 */
	public function massFeaturedBrandAction(){
		$brandIds = $this->getRequest()->getParam('brand');
		if(!is_array($brandIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brands')->__('Please select brands.'));
		} 
		else {
			try {
				foreach ($brandIds as $brandId) {
				$brand = Mage::getSingleton('brands/brand')->load($brandId)
							->setFeaturedBrand($this->getRequest()->getParam('flag_featured_brand'))
							->setIsMassupdate(true)
							->save();
				}
				$this->_getSession()->addSuccess($this->__('Total of %d brands were successfully updated.', count($brandIds)));
			}
			catch (Mage_Core_Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('brands')->__('There was an error updating brands.'));
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/index');
	}
	/**
	 * get grid of products action
	 * @access public
	 */
	public function productsAction(){
		$this->_initBrand();
		$this->loadLayout();
		$this->getLayout()->getBlock('brand.edit.tab.product')
			->setBrandProducts($this->getRequest()->getPost('brand_products', null));
		$this->renderLayout();
	}
	/**
	 * get grid of products action
	 * @access public
	 */
	public function productsgridAction(){
		$this->_initBrand();
		$this->loadLayout();
		$this->getLayout()->getBlock('brand.edit.tab.product')
			->setBrandProducts($this->getRequest()->getPost('brand_products', null));
		$this->renderLayout();
	}
	/**
	 * export as csv - action
	 * @access public
	 */
	public function exportCsvAction(){
		$fileName   = 'modulemart_brands.csv';
		$content	= $this->getLayout()->createBlock('brands/adminhtml_brand_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as MsExcel - action
	 * @access public
	 */
	public function exportExcelAction(){
		$fileName   = 'modulemart_brands.xls';
		$content	= $this->getLayout()->createBlock('brands/adminhtml_brand_grid')->getExcelFile();
		$this->_prepareDownloadResponse($fileName, $content);
	}
	/**
	 * export as xml - action
	 * @access public
	 */
	public function exportXmlAction(){
		$fileName   = 'modulemart_brands.xml';
		$content	= $this->getLayout()->createBlock('brands/adminhtml_brand_grid')->getXml();
		$this->_prepareDownloadResponse($fileName, $content);
	}
}