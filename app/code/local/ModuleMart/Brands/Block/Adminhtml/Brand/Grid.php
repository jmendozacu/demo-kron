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
class ModuleMart_Brands_Block_Adminhtml_Brand_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	/**
	 * constructor
	 * @access public
	 */
	public function __construct(){
		parent::__construct();
		$this->setId('brandGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	/**
	 * prepare collection
	 * @access protected
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('brands/brand')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	/**
	 * prepare grid collection
	 * @access protected
	 */
	protected function _prepareColumns(){
		$this->addColumn('entity_id', array(
			'header'	=> Mage::helper('brands')->__('Brand Id'),
			'index'		=> 'entity_id',
			'type'		=> 'number',
			'width'     => '55px',
		));
		$this->addColumn('brand_logo', array(
            'header'    => Mage::helper('brands')->__('Logo'),
            'align'     => 'left',
            'width'     => '55px',
            'index'     => 'brand_logo',
           // 'type'      => 'image',
            'escape'    => true,
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => new ModuleMart_Brands_Block_Adminhtml_Brand_Renderer_Logo,
       ));
	   $this->addColumn('brand_name', array(
			'header'=> Mage::helper('brands')->__('Brand Name'),
			'index' => 'brand_name',
			'type'	 	=> 'text',

		));
		$this->addColumn('url_key', array(
			'header'	=> Mage::helper('brands')->__('URL key'),
			'index'		=> 'url_key',
		));
		$this->addColumn('brand_ids', array(
            'header'    => Mage::helper('brands')->__('Product(s)'),
            'align'     => 'left',
            'width'     => '45px',
            'renderer'  => new ModuleMart_Brands_Block_Adminhtml_Brand_Renderer_Count,
       ));
	
		$this->addColumn('featured_brand', array(
			'header'=> Mage::helper('brands')->__('Featured'),
			'index' => 'featured_brand',
			'type'		=> 'options',
			'options'	=> array(
				'1' => Mage::helper('brands')->__('Yes'),
				'0' => Mage::helper('brands')->__('No'),
			)

		));
		
		$this->addColumn('is_on_top', array(
			'header'=> Mage::helper('brands')->__('Include in Navigation Menu'),
			'index' => 'is_on_top',
			'type'		=> 'options',
			'options'	=> array(
				'1' => Mage::helper('brands')->__('Yes'),
				'0' => Mage::helper('brands')->__('No'),
			)

		));
		
		$this->addColumn('status', array(
			'header'	=> Mage::helper('brands')->__('Status'),
			'index'		=> 'status',
			'type'		=> 'options',
			'options'	=> array(
				'1' => Mage::helper('brands')->__('Enabled'),
				'0' => Mage::helper('brands')->__('Disabled'),
			)
		));
		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', array(
				'header'=> Mage::helper('brands')->__('Store Views'),
				'index' => 'store_id',
				'type'  => 'store',
				'store_all' => true,
				'store_view'=> true,
				'sortable'  => false,
				'filter_condition_callback'=> array($this, '_filterStoreCondition'),
			));
		}
		$this->addColumn('created_at', array(
			'header'	=> Mage::helper('brands')->__('Created at'),
			'index' 	=> 'created_at',
			'width' 	=> '120px',
			'type'  	=> 'datetime',
		));

		$this->addColumn('action',
			array(
				'header'=>  Mage::helper('brands')->__('Action'),
				'width' => '100',
				'type'  => 'action',
				'getter'=> 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('brands')->__('Edit'),
						'url'   => array('base'=> '*/*/edit'),
						'field' => 'id'
					)
				),
				'filter'=> false,
				'is_system'	=> true,
				'sortable'  => false,
		));
		$this->addExportType('*/*/exportCsv', Mage::helper('brands')->__('CSV'));
		$this->addExportType('*/*/exportExcel', Mage::helper('brands')->__('Excel'));
		$this->addExportType('*/*/exportXml', Mage::helper('brands')->__('XML'));
		return parent::_prepareColumns();
	}
	/**
	 * prepare mass action
	 * @access protected
	 */
	protected function _prepareMassaction(){
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('brand');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> Mage::helper('brands')->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete'),
			'confirm'  => Mage::helper('brands')->__('Are you sure?')
		));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('brands')->__('Change status'),
			'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'status' => array(
						'name' => 'status',
						'type' => 'select',
						'class' => 'required-entry',
						'label' => Mage::helper('brands')->__('Status'),
						'values' => array(
								'1' => Mage::helper('brands')->__('Enabled'),
								'0' => Mage::helper('brands')->__('Disabled'),
						)
				)
			)
		));
		$this->getMassactionBlock()->addItem('featured_brand', array(
			'label'=> Mage::helper('brands')->__('Change Featured'),
			'url'  => $this->getUrl('*/*/massFeaturedBrand', array('_current'=>true)),
			'additional' => array(
				'flag_featured_brand' => array(
						'name' => 'flag_featured_brand',
						'type' => 'select',
						'class' => 'required-entry',
						'label' => Mage::helper('brands')->__('Featured'),
						'values' => array(
								'1' => Mage::helper('brands')->__('Yes'),
								'0' => Mage::helper('brands')->__('No'),
						)
				)
			)
		));
		return $this;
	}
	/**
	 * get the row url
	 * @access public
	 */
	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	/**
	 * get the grid url
	 * @access public
	 */
	public function getGridUrl(){
		return $this->getUrl('*/*/grid', array('_current'=>true));
	}
	/**
	 * after collection load
	 * @access protected
	 */
	protected function _afterLoadCollection(){
		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();
	}
	/**
	 * filter store column
	 * @access protected
	 */
	protected function _filterStoreCondition($collection, $column){
		if (!$value = $column->getFilter()->getValue()) {
        	return;
		}
		$collection->addStoreFilter($value);
		return $this;
    }
}