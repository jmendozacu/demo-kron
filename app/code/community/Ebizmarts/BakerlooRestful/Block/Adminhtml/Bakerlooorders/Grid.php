<?php

class Ebizmarts_BakerlooRestful_Block_Adminhtml_Bakerlooorders_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();

        $this->setId('bakerlooOrders');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setDefaultSort('id');
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('bakerloo_restful/order')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('id', array(
            'header' => Mage::helper('bakerloo_restful')->__('ID'),
            'index' => 'id',
            'type' => 'number',
        ));
        $this->addColumn('order_increment_id', array(
            'header' => Mage::helper('bakerloo_restful')->__('Order #'),
            'index' => 'order_increment_id',
            'renderer' => 'bakerloo_restful/adminhtml_widget_grid_column_renderer_orderNumber',
        ));
        $this->addColumn('device_order_id', array(
            'header' => Mage::helper('bakerloo_restful')->__('Device Order #'),
            'index' => 'device_order_id',
        ));
        $this->addColumn('device_id', array(
            'header' => Mage::helper('bakerloo_restful')->__('Device'),
            'index' => 'device_id',
        ));
        $this->addColumn('remote_ip', array(
            'header' => Mage::helper('bakerloo_restful')->__('Remote IP'),
            'index' => 'remote_ip',
        ));
        $this->addColumn('subtotal', array(
            'header' => Mage::helper('bakerloo_restful')->__('Gross'),
            'index' => 'subtotal',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));
        $this->addColumn('grand_total', array(
            'header' => Mage::helper('bakerloo_restful')->__('Total'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
        ));
        $this->addColumn('admin_user', array(
            'header' => Mage::helper('bakerloo_restful')->__('User'),
            'index' => 'admin_user',
        ));
        $this->addColumn('admin_user_auth', array(
            'header' => Mage::helper('bakerloo_restful')->__('Super User'),
            'index' => 'admin_user_auth',
        ));
        $this->addColumn('created_at', array(
            'header' => Mage::helper('bakerloo_restful')->__('Createad At'),
            'index' => 'created_at',
            'type' => 'datetime',
        ));
        $this->addColumn('updated_at', array(
            'header' => Mage::helper('bakerloo_restful')->__('Updated At'),
            'index' => 'updated_at',
            'type' => 'datetime',
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('bakerloo_restful')->__('Action'),
            'width' => '80px',
            'type' => 'action',
            'align' => 'center',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('bakerloo_restful')->__('Try Again'),
                    'url' => array('base' => 'adminhtml/bakerlooorders/place'),
                    'field' => 'id',
                    'confirm' => Mage::helper('bakerloo_restful')->__('Are you sure? This will try to create a new order in Magento.')
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('bakerloo_restful')->__('CSV'));

        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Return row url for js event handlers
     *
     * @param Varien_Object
     * @return string
     */
    public function getRowUrl($item) {
        return $this->getUrl('*/*/edit', array('id' => $item->getId()));
    }

}