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
 * Simipos Location Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Block_Adminhtml_Location_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('simiposLocationGrid');
        $this->setDefaultSort('location_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_SimiPOS_Block_Adminhtml_Location_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('simipos/location_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_SimiPOS_Block_Adminhtml_Location_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('location_id', array(
            'header'    => Mage::helper('simipos')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'location_id',
            'type'      => 'number',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('simipos')->__('Location Name'),
            'align'     => 'left',
            'index'     => 'name',
        ));

        $this->addColumn('address', array(
            'header'    => Mage::helper('simipos')->__('Address'),
            'align'     =>'left',
            'index'     => 'address',
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
    
    /**
     * prepare mass action for this grid
     *
     * @return Magestore_SimiPOS_Block_Adminhtml_Location_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('location_id');
        $this->getMassactionBlock()->setFormFieldName('simipos');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'        => Mage::helper('simipos')->__('Delete'),
            'url'        => $this->getUrl('*/*/massDelete'),
            'confirm'    => Mage::helper('simipos')->__('Are you sure?')
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
