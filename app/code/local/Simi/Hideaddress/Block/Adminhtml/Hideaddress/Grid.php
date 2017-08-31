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
 * @package     Magestore_Hideaddress
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Hideaddress Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Hideaddress
 * @author      Magestore Developer
 */
class Simi_Hideaddress_Block_Adminhtml_Hideaddress_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('hideaddressGrid');
        $this->setDefaultSort('hideaddress_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Simi_Hideaddress_Block_Adminhtml_Hideaddress_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('hideaddress/hideaddress')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Simi_Hideaddress_Block_Adminhtml_Hideaddress_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('hideaddress_id', array(
            'header'    => Mage::helper('hideaddress')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'hideaddress_id',
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('hideaddress')->__('Title'),
            'align'     =>'left',
            'index'     => 'title',
        ));

        $this->addColumn('content', array(
            'header'    => Mage::helper('hideaddress')->__('Item Content'),
            'width'     => '150px',
            'index'     => 'content',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('hideaddress')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'        => 'options',
            'options'     => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));

        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('hideaddress')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('hideaddress')->__('Edit'),
                        'url'        => array('base'=> '*/*/edit'),
                        'field'        => 'id'
                    )),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('hideaddress')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('hideaddress')->__('XML'));

        return parent::_prepareColumns();
    }
    
    /**
     * prepare mass action for this grid
     *
     * @return Simi_Hideaddress_Block_Adminhtml_Hideaddress_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('hideaddress_id');
        $this->getMassactionBlock()->setFormFieldName('hideaddress');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'        => Mage::helper('hideaddress')->__('Delete'),
            'url'        => $this->getUrl('*/*/massDelete'),
            'confirm'    => Mage::helper('hideaddress')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('hideaddress/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('hideaddress')->__('Change status'),
            'url'    => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name'    => 'status',
                    'type'    => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('hideaddress')->__('Status'),
                    'values'=> $statuses
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
}