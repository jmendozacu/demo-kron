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
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Madapter Grid Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @author  	Magestore Developer
 */
class Magestore_Madapter_Block_Adminhtml_Madapter_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('madapterGrid');
        $this->setDefaultSort('madapter_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Madapter_Block_Adminhtml_Madapter_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('madapter/madapter')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Madapter_Block_Adminhtml_Madapter_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('madapter_id', array(
            'header' => Mage::helper('madapter')->__('#ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'madapter_id',
        ));

        $this->addColumn('order_id', array(
            'header' => Mage::helper('madapter')->__('Order ID'),
            'align' => 'left',
            'index' => 'title',
        ));

        $this->addColumn('transaction_id', array(
            'header' => Mage::helper('madapter')->__('Transaction ID'),
            'width' => '150px',
            'index' => 'content',
        ));

        $this->addColumn('transaction_name', array(
            'header' => Mage::helper('madapter')->__('Fund Source Type'),
            'width' => '150px',
            'index' => 'content',
        ));

        $this->addColumn('transaction_emails', array(
            'header' => Mage::helper('madapter')->__('Email'),
            'width' => '150px',
            'index' => 'content',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('madapter')->__('Transaction Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('madapter')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('madapter')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
            )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('madapter')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('madapter')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Madapter_Block_Adminhtml_Madapter_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('madapter_id');
        $this->getMassactionBlock()->setFormFieldName('madapter');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('madapter')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('madapter')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('madapter/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('madapter')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('madapter')->__('Status'),
                    'values' => $statuses
            ))
        ));
        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}