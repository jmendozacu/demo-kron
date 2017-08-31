<?php

class Devinc_Groupdeals_Block_Adminhtml_Merchants_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('merchantsGrid');
        $this->setDefaultSort('merchants_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
  
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('groupdeals/merchants')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
  
    protected function _prepareColumns()
    {
        $this->addColumn('merchants_id', array(
            'header'    => Mage::helper('groupdeals')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'merchants_id',
        )); 
  	  
        $this->addColumn('name', array(
            'header'    => Mage::helper('groupdeals')->__('Name'),
            'align'     => 'left',
            'index'     => 'name',
            'renderer'  => 'groupdeals/adminhtml_merchants_grid_renderer_name',
        )); 
  	  
        $this->addColumn('email', array(
            'header'    => Mage::helper('groupdeals')->__('Email'),
            'align'     => 'left',
            'index'     => 'email',
            'width'     => '150px',
            'renderer'  => 'groupdeals/adminhtml_merchants_grid_renderer_email',
        )); 
  	  
        $this->addColumn('status', array(
            'header'    => Mage::helper('groupdeals')->__('Status'),
            'align'     => 'left',
            'index'     => 'status',
            'type'      => 'options',
            'width'     => '100px',
            'options'   => array(
                1 => 'Enabled',
                2 => 'Disabled',
                3 => 'Pending Approval',
            ),
            'renderer'  => 'groupdeals/adminhtml_merchants_grid_renderer_status',
        ));
  	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('groupdeals')->__('Action'),
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('groupdeals')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    ),		
                    array(
                        'caption'   => Mage::helper('groupdeals')->__('Delete'),
                        'url'       => array('base'=> '*/*/delete'),
                        'field'     => 'id',
  		  			'confirm'  => Mage::helper('groupdeals')->__('Are you sure you want to delete this merchant?')
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
                'width'     => '100px',
        ));
  		
  		//$this->addExportType('*/*/exportCsv', Mage::helper('groupdeals')->__('CSV'));
  	  
        return parent::_prepareColumns();
    }
  
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('merchants_id');
        $this->getMassactionBlock()->setFormFieldName('merchants');
  
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('groupdeals')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('groupdeals')->__('Are you sure you want to delete these merchants?')
        ));
  
        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();
  
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('groupdeals')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('groupdeals')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }
  
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
  
}