<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simipos User Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Block_Adminhtml_User_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('simiposUserGrid');
        $this->setDefaultSort('user_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_SimiPOS_Block_Adminhtml_User_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('simipos/user_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_SimiPOS_Block_Adminhtml_User_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('user_id', array(
            'header'    => Mage::helper('simipos')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'user_id',
            'type'      => 'number',
        ));
//
//        $this->addColumn('username', array(
//            'header'    => Mage::helper('simipos')->__('User Name'),
//            'align'     => 'left',
//            'index'     => 'username',
//        ));

        $this->addColumn('email', array(
            'header'    => Mage::helper('simipos')->__('Email'),
            'align'     =>'left',
            'index'     => 'email',
        ));
        
        $this->addColumn('user_role', array(
            'header'    => Mage::helper('simipos')->__('Role'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'user_role',
            'type'      => 'options',
            'options'   => Mage::getSingleton('simipos/role')->getOptionArray(),
        ));
        
        $locations = Mage::getSingleton('simipos/location')->getOptionArray();
        $locations['0'] = Mage::helper('simipos')->__('Unlocated');
        $this->addColumn('location_id', array(
            'header'    => Mage::helper('simipos')->__('Location'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'location_id',
            'type'      => 'options',
            'options'   => $locations,
            'renderer'  => 'simipos/adminhtml_user_renderer_location',
            'filter_condition_callback' => array($this, '_filterLocation'),
        ));
        
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('stores', array(
                'header'    => Mage::helper('simipos')->__('Store View'),
                'index'         => 'stores',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback' => array($this, '_filterStoreCondition'),
            ));
        }
        
        Mage::dispatchEvent('simipos_block_user_grid_columns', array('block' => $this));
        
        $this->addColumn('created_time', array(
            'header'    => Mage::helper('simipos')->__('Created On'),
            'index'     => 'created_time',
            'width'     => '120px',
            'type'      => 'datetime',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('simipos')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('simipos/status')->getOptionArray(),
        ));

        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('simipos')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'    => Mage::helper('simipos')->__('Edit'),
                        'url'        => array('base'=> '*/*/edit'),
                        'field'      => 'id'
                    )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('simipos')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('simipos')->__('XML'));

        return parent::_prepareColumns();
    }
    
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        return parent::_afterLoadCollection();
    }
    
    protected function _filterLocation($collection, $column)
    {
    	$field = ( $column->getFilterIndex() ) ? $column->getFilterIndex() : $column->getIndex();
    	$cond = $column->getFilter()->getCondition();
    	if ($field && isset($cond)) {
    		if ($value = $column->getFilter()->getValue()) {
    			$collection->addFieldToFilter($field , $cond);
    		} else {
    			$collection->getSelect()
    			    ->where("$field = 0 OR $field IS NULL");
    		}
    	}
    }
    
    protected function _filterStoreCondition($collection, $column)
    {
        if ($value = $column->getFilter()->getValue()) {
            $this->getCollection()->addStoreFilter($value);
        }
    }
    
    /**
     * prepare mass action for this grid
     *
     * @return Magestore_SimiPOS_Block_Adminhtml_User_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('user_id');
        $this->getMassactionBlock()->setFormFieldName('simipos');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'        => Mage::helper('simipos')->__('Delete'),
            'url'        => $this->getUrl('*/*/massDelete'),
            'confirm'    => Mage::helper('simipos')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('simipos/status')->getOptionArray();
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('simipos')->__('Change Status'),
            'url'    => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility'  => array(
                    'name'    => 'status',
                    'type'    => 'select',
                    'class'   => 'required-entry',
                    'label'   => Mage::helper('simipos')->__('Status'),
                    'values'  => $statuses
                ))
        ));
        
        $locations = Mage::getSingleton('simipos/location')->getOptionHash();
        array_unshift($locations, array(
            'value' => '0',
            'label' => Mage::helper('simipos')->__('Unlocated')
        ));
        $this->getMassactionBlock()->addItem('location', array(
            'label'  => Mage::helper('simipos')->__('Change Location'),
            'url'    => $this->getUrl('*/*/massLocation', array('_current'=>true)),
            'additional' => array(
                'visibility'  => array(
                    'name'    => 'location',
                    'type'    => 'select',
                    'class'   => 'required-entry',
                    'label'   => Mage::helper('simipos')->__('Location'),
                    'values'  => $locations
                ))
        ));
        
        return $this;
    }
    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    /**
     * get grid url (use for ajax load)
     * 
     * @return string
     */
    public function getGridUrl()
    {
       return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
