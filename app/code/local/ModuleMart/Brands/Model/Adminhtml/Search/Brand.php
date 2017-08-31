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
class ModuleMart_Brands_Model_Adminhtml_Search_Brand extends Varien_Object{
	/**
	 * Load search results
	 * @access public
	 */
	public function load(){
		$arr = array();
		if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
			$this->setResults($arr);
			return $this;
		}
		$collection = Mage::getResourceModel('brands/brand_collection')
			->addFieldToFilter('brand_name', array('like' => $this->getQuery().'%'))
			->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
			->load();
		foreach ($collection->getItems() as $brand) {
			$arr[] = array(
				'id'=> 'brand/1/'.$brand->getId(),
				'type'  => Mage::helper('brands')->__('Brand'),
				'name'  => $brand->getBrandName(),
				'description'   => $brand->getBrandName(),
				'url' => Mage::helper('adminhtml')->getUrl('*/brands_brand/edit', array('id'=>$brand->getId())),
			);
		}
		$this->setResults($arr);
		return $this;
	}
}