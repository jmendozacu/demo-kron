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
class ModuleMart_Brands_Block_Adminhtml_Catalog_Product_Edit_Tab_Brand extends Mage_Adminhtml_Block_Widget_Grid{
	/**
	 * Set grid params
	 * @access protected
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('brand_grid');
		$this->setDefaultSort('position');
		$this->setDefaultDir('ASC');
		$this->setUseAjax(true);
		if ($this->getProduct()->getId()) {
			$this->setDefaultFilter(array('in_brands'=>1));
		}
	}
	/**
	 * prepare the brand collection
	 * @access protected 
	 */
	protected function _prepareCollection() {
		$collection = Mage::getResourceModel('brands/brand_collection');
		if ($this->getProduct()->getId()){
			$constraint = 'related.product_id='.$this->getProduct()->getId();
			}
			else{
				$constraint = 'related.product_id=0';
			}
		$collection->getSelect()->joinLeft(
			array('related'=>$collection->getTable('brands/brand_product')),
			'related.brand_id=main_table.entity_id AND '.$constraint,
			array('position')
		);
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}
	/**
	 * prepare mass action grid
	 * @access protected
	 */ 
	protected function _prepareMassaction(){
		return $this;
	}
	/**
	 * prepare the grid columns
	 * @access protected
	 */
	protected function _prepareColumns(){
		$this->addColumn('in_brands', array(
			'header_css_class'  => 'a-center',
			'type'  => 'checkbox',
			'name'  => 'in_brands',
			'values'=> $this->_getSelectedBrands(),
			'align' => 'center',
			'index' => 'entity_id'
		));
		$this->addColumn('brand_name', array(
			'header'=> Mage::helper('brands')->__('Name'),
			'align' => 'left',
			'index' => 'brand_name',
		));
		$this->addColumn('position', array(
			'header'		=> Mage::helper('brands')->__('Position'),
			'name'  		=> 'position',
			'width' 		=> 60,
			'type'		=> 'number',
			'validate_class'=> 'validate-number',
			'index' 		=> 'position',
			'editable'  	=> true,
		));
	}
	/**
	 * Retrieve selected brands
	 * @access protected
	 */
	protected function _getSelectedBrands(){
		$brands = $this->getProductBrands();
		if (!is_array($brands)) {
			$brands = array_keys($this->getSelectedBrands());
		}
		return $brands;
	}
 	/**
	 * Retrieve selected brands
	 * @access protected
	 */
	public function getSelectedBrands() {
		$brands = array();
		//used helper here in order not to override the product model
		$selected = Mage::helper('brands/product')->getSelectedBrands(Mage::registry('current_product'));
		if (!is_array($selected)){
			$selected = array();
		}
		foreach ($selected as $brand) {
			$brands[$brand->getId()] = array('position' => $brand->getPosition());
		}
		return $brands;
	}
	/**
	 * get row url
	 * @access public
	 */
	public function getRowUrl($item){
		return '#';
	}
	/**
	 * get grid url
	 * @access public
	 */
	public function getGridUrl(){
		return $this->getUrl('*/*/brandsGrid', array(
			'id'=>$this->getProduct()->getId()
		));
	}
	/**
	 * get the current product
	 * @access public
	 */
	public function getProduct(){
		return Mage::registry('current_product');
	}
	/**
	 * Add filter
	 * @access protected
	 * @param object $column
	 */
	protected function _addColumnFilterToCollection($column){
		if ($column->getId() == 'in_brands') {
			$brandIds = $this->_getSelectedBrands();
			if (empty($brandIds)) {
				$brandIds = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('entity_id', array('in'=>$brandIds));
			} 
			else {
				if($brandIds) {
					$this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$brandIds));
				}
			}
		} 
		else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}
}