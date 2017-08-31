<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Tab_Orders extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ordersGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        $this->setDefaultSort('real_order_id');
        $this->setDefaultDir('DESC');
        $this->setVarNameFilter('orders_filter');
    }

	protected function _prepareCollection()
    {	
		$orderIds = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('product_id', $this->getRequest()->getParam('id'))->getColumnValues('order_id');	
			
		$collection = Mage::getResourceModel('sales/order_grid_collection')->addAttributeToFilter('entity_id', array('in' => $orderIds));
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            ));
        }

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
        ));

        $this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Order Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));
	    
		//reformat coupon_sent column in case it's for csv, to not contain html tags
		if ($this->getIsCsv()) {
			$renderer = 'groupdeals/adminhtml_groupdeals_edit_renderer_couponcolumncsv';
		} else {
			$renderer = 'groupdeals/adminhtml_groupdeals_edit_renderer_couponcolumn';		
		}
	    
		$product = Mage::registry('current_product');
		if ($product->getIsVirtual()) {
		     $this->addColumn('coupon_sent', array(
		     	  'header'    => Mage::helper('groupdeals')->__('Coupon Code'),
		     	  'align'     => 'left',
		     	  'index'     => 'entity_id',
		     	  'filter' 	  => false,
		     	  'width'     => '100px',
		     	  'type'      => 'text',
		     	  'renderer'  => $renderer,
		     ));
		}

		if (!$this->getIsCsv()) {
        	if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
				$this->addColumn('action', array(
				  'header'    => Mage::helper('groupdeals')->__('Action'),
				  'align'     => 'left',
				  'index'     => 'entity_id',
				  'filter' 	  => false,
				  'width'     => '100px',
				  'type'      => 'action',
				  'renderer'  => 'groupdeals/adminhtml_groupdeals_edit_renderer_actioncolumn',
				));			
        	}
        }

        $this->addExportType('*/*/exportOrdersCsv/csv_excel_name/'.$product->getName(), Mage::helper('sales')->__('CSV'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
			return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }
	
    public function getGridUrl()
    {
        return $this->getUrl('*/*/orders', array('_current'=>true));
    }

}