<?php

class Devinc_Groupdeals_Block_Adminhtml_Subscribers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('subscribersGrid');
        $this->setDefaultSort('subscriber_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
  
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('groupdeals/subscribers')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
  
    protected function _prepareColumns()
    {
        $this->addColumn('subscriber_id', array(
            'header'    => Mage::helper('groupdeals')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'subscriber_id',
        )); 
  	  
        $this->addColumn('email', array(
            'header'    => Mage::helper('groupdeals')->__('Email'),
            'align'     =>'left',
            'index'     => 'email',
        )); 
  	  
  	    if (!Mage::app()->isSingleStoreMode()) {
  	    	  $this->addColumn('store_id', array(
  	    		  'header'    		=> Mage::helper('groupdeals')->__('Subscribed to (Store)'),
  	    		  'index'     		=> 'store_id',
  	    		  'type'      		=> 'store',
  	    	      'width'     		=>'200px',
  	    		  'store_view'		=> true,
  	    		  'display_deleted' => false,
  	    	  ));
  	    }
  	  
        $this->addColumn('city', array(
            'header'    => Mage::helper('groupdeals')->__('City'),
            'align'     =>'left',
  		    'width'     =>'200px',
            'index'     => 'city',
        )); 
  	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('groupdeals')->__('Action'),
                'type'      => 'action',
                'getter'    => 'getId',
  		  		'width'     =>'150px',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('groupdeals')->__('Unsubscribe'),
                        'url'       => array('base'=> '*/*/delete'),
                        'field'     => 'id',
  		  				'confirm'  => Mage::helper('groupdeals')->__('Are you sure you want to unsubscribe this customer?')
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
  		
  		$this->addExportType('*/*/exportCsv', Mage::helper('groupdeals')->__('CSV'));
  	  
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('subscriber_id');
        $this->getMassactionBlock()->setFormFieldName('subscribers');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('groupdeals')->__('Unsubscribe'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('groupdeals')->__('Are you sure you want to unsubscribe these customers?')
        ));
      
        return $this;
    }

}