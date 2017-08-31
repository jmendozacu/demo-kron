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
 * @package     Magestore_Themeone
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Themeone Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Themeone
 * @author      Magestore Developer
 */
class Simi_Themeone_Block_Adminhtml_Themeone_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('themeoneGrid');
        $this->setDefaultSort('themeone_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Simi_Themeone_Block_Adminhtml_Themeone_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('themeone/themeone')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Simi_Themeone_Block_Adminhtml_Themeone_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('themeone_id', array(
            'header'    => Mage::helper('themeone')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'themeone_id',
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('themeone')->__('Title'),
            'align'     =>'left',
            'index'     => 'title',
        ));

        $this->addColumn('content', array(
            'header'    => Mage::helper('themeone')->__('Item Content'),
            'width'     => '150px',
            'index'     => 'content',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('themeone')->__('Status'),
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
                'header'    =>    Mage::helper('themeone')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('themeone')->__('Edit'),
                        'url'        => array('base'=> '*/*/edit'),
                        'field'        => 'id'
                    )),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('themeone')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('themeone')->__('XML'));

        return parent::_prepareColumns();
    }
    
    /**
     * prepare mass action for this grid
     *
     * @return Simi_Themeone_Block_Adminhtml_Themeone_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('themeone_id');
        $this->getMassactionBlock()->setFormFieldName('themeone');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'        => Mage::helper('themeone')->__('Delete'),
            'url'        => $this->getUrl('*/*/massDelete'),
            'confirm'    => Mage::helper('themeone')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('themeone/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('themeone')->__('Change status'),
            'url'    => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name'    => 'status',
                    'type'    => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('themeone')->__('Status'),
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