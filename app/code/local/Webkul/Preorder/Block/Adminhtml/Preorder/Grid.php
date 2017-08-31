<?php
  
  class Webkul_Preorder_Block_Adminhtml_Preorder_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    public function __construct() {
        parent::__construct();
        $this->setId('preorderGrid');
        $this->setDefaultSort('preorder_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $orders = array();
        $preorders = Mage::getModel('preorder/preorder')->getCollection()->addFieldToFilter('rand',1);
        foreach ($preorders as $value) {
          $orders[] = $value->getOrderid();
        }
        $collection = Mage::getResourceModel('sales/order_grid_collection')->addFieldToFilter('entity_id',array('in'=>$orders));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
      $this->addColumn('real_order_id', array(
          'header'    =>    Mage::helper('sales')->__('Order #'),
          'width'     =>    '80px',
          'type'      =>    'text',
          'index'     =>    'increment_id',
      ));

      $this->addColumn('created_at', array(
          'header'    =>    Mage::helper('sales')->__('Purchased On'),
          'index'     =>    'created_at',
          'type'      =>    'datetime',
          'width'     =>    '100px',
      ));

      $this->addColumn('billing_name', array(
          'header'    =>    Mage::helper('sales')->__('Bill to Name'),
          'index'     =>    'billing_name',
      ));

      $this->addColumn('shipping_name', array(
          'header'    =>    Mage::helper('sales')->__('Ship to Name'),
          'index'     =>    'shipping_name',
      ));

      $this->addColumn('base_grand_total', array(
          'header'    =>    Mage::helper('sales')->__('G.T. (Base)'),
          'index'     =>    'base_grand_total',
          'type'      =>    'currency',
          'currency'  =>    'base_currency_code',
      ));

      $this->addColumn('grand_total', array(
          'header'    =>    Mage::helper('sales')->__('G.T. (Purchased)'),
          'index'     =>    'grand_total',
          'type'      =>    'currency',
          'currency'  =>    'order_currency_code',
      ));

      $this->addColumn('status', array(
          'header'    =>    Mage::helper('sales')->__('Status'),
          'index'     =>    'status',
          'type'      =>    'options',
          'width'     =>    '70px',
          'options'   => Mage::getSingleton('sales/order_config')->getStatuses(),
      ));
      if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
          $this->addColumn('action',
              array(
                  'header'    =>    Mage::helper('sales')->__('Action'),
                  'width'     =>    '50px',
                  'type'      =>    'action',
                  'getter'    =>    'getId',
                  'actions'   =>    array(
                                      array(
                                          'caption'     =>    Mage::helper('sales')->__('View'),
                                          'url'         =>    array('base'    =>    'adminhtml/sales_order/view'),
                                          'field'       =>    'order_id'
                                      )
                                    ),
                  'filter'    =>    false,
                  'sortable'  =>    false,
                  'index'     =>    'stores',
                  'is_system' =>    true,
          ));
      }
  		
  		$this->addExportType('*/*/exportCsv', Mage::helper('preorder')->__('CSV'));
  		$this->addExportType('*/*/exportXml', Mage::helper('preorder')->__('XML'));
      return parent::_prepareColumns();
    }

      public function getRowUrl($row)
      {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
      }
    protected function _prepareMassaction()
    {
          $this->setMassactionIdField('order_id');
          $this->getMassactionBlock()->setFormFieldName('preorder');

          $this->getMassactionBlock()->addItem('Email', array(
               'label'    => Mage::helper('preorder')->__('Email'),
               'url'      => $this->getUrl('*/*/massEmail'),
               'confirm'  => Mage::helper('preorder')->__('Are you sure?')
          ));
          return $this;
      }
  }