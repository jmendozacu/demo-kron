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
 * Simipos Location's User Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Block_Adminhtml_Location_Edit_Tab_Users extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('simipos_location_users_grid');
        $this->setDefaultSort('users_user_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('simipos/user_collection');
        $collection->addFieldToFilter('location_id', $this->getRequest()->getParam('id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('users_user_id', array(
            'header'    => Mage::helper('simipos')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'user_id',
            'type'      => 'number',
        ));

        $this->addColumn('users_email', array(
            'header'    => Mage::helper('simipos')->__('Email'),
            'align'     => 'left',
            'index'     => 'email',
        ));
        
        $this->addColumn('users_user_role', array(
            'header'    => Mage::helper('simipos')->__('Role'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'user_role',
            'type'      => 'options',
            'options'   => Mage::getSingleton('simipos/role')->getOptionArray(),
        ));
        
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('users_stores', array(
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
        
        $this->addColumn('users_created_time', array(
            'header'    => Mage::helper('simipos')->__('Created On'),
            'index'     => 'created_time',
            'width'     => '120px',
            'type'      => 'datetime',
        ));

        $this->addColumn('users_status', array(
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
                        'url'        => array('base'=> '*/adminhtml_user/edit'),
                        'field'      => 'id'
                    )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        return parent::_prepareColumns();
    }
    
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        return parent::_afterLoadCollection();
    }
    
    protected function _filterStoreCondition($collection, $column)
    {
        if ($value = $column->getFilter()->getValue()) {
            $this->getCollection()->addStoreFilter($value);
        }
    }
    
    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('simipos/simipos')) {
            return $this->getUrl('*/adminhtml_user/edit', array('id' => $row->getId()));
        }
        return false;
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/users', array('_current'=>true));
    }
}
