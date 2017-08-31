<?php
/*
 * Developer: Michael Jacky
 * Team site: http://cmsideas.net/
 * Support: http://support.cmsideas.net/
 * 
 */ 
class Glace_Extendedordergrid_Model_Observer 
{
    protected $_orderTableAlias = 'cmsideas_order_item';
    protected $_extrOrderColumnPrefix = 'extra_col_';
    
    public function onSalesOrderItemSaveAfter($observer){
        
        $orderItem = $observer->getEvent()->getItem();
        
        $amOrderItem = Mage::getModel("ciextendedordergrid/order_item");
                
        $amOrderItem->mapOrder($orderItem);
        
        return true;  
    } 
    
    protected function prepareOrderCollectionJoins(&$collection, $orderItemsColumns = array()){
        $showShipping = Mage::getStoreConfig('ciextendedordergrid/general/shipping');
        $showPayment = Mage::getStoreConfig('ciextendedordergrid/general/payment');
        $showCoupon = Mage::getStoreConfig('ciextendedordergrid/general/coupon');
        $showCustomerEmail = Mage::getStoreConfig('ciextendedordergrid/general/customer_email');
        
        $showShippingAddress = Mage::getStoreConfig('ciextendedordergrid/general/shipping_address');
        $showBillingAddress = Mage::getStoreConfig('ciextendedordergrid/general/billing_address');
        
        
        $excludeStatuses = Mage::getStoreConfig('ciextendedordergrid/general/exclude');
        $excludeStatuses = !empty($excludeStatuses) ? explode(',', $excludeStatuses) : array();
        if(Mage::app()->getRequest()->getControllerName() == 'sales_order'
        && Mage::app()->getRequest()->getActionName() == 'index') {
             $collection->getSelect()->join(
                 array(
                     'order_item' => $collection->getTable('sales/order_item')
                 ),
                 'main_table.entity_id = order_item.order_id',
                 array()
             );

            if ($showCoupon || $showShipping || $showCustomerEmail){
                $collection->getSelect()->join(
                    array(
                        'order' => $collection->getTable('sales/order')
                    ),
                    'main_table.entity_id = order.entity_id',
                    array(
                        'order.coupon_code as cmsideas_coupon_code',
                        'order.shipping_description as cmsideas_shipping_description',
                        'order.customer_email as cmsideas_customer_email')
                );
            }
        }
        if ($showPayment){
            $collection->getSelect()->joinLeft(
                array(
                    'order_payment' => $collection->getTable('sales/order_payment')
                ),
                'main_table.entity_id = order_payment.parent_id', 
                array('order_payment.method as cmsideas_method')
            );
        }
        
        if ($showShippingAddress){
            $collection->getSelect()->joinLeft(
                array(
                    'shipping_order_address' => $collection->getTable('sales/order_address')
                ),
                'main_table.entity_id = shipping_order_address.parent_id and shipping_order_address.address_type = \'shipping\'', 
                array(
                )
            );
        }
        
        if ($showBillingAddress){
            $collection->getSelect()->joinLeft(
                array(
                    'billing_order_address' => $collection->getTable('sales/order_address')
                ),
                'main_table.entity_id = billing_order_address.parent_id and billing_order_address.address_type = \'billing\'', 
                array(
                )
            );
        }

        if(Mage::app()->getRequest()->getControllerName() == 'sales_order'
            && Mage::app()->getRequest()->getActionName() == 'index') {
            $collection->getSelect()->joinLeft(
                array(
                    $this->_orderTableAlias => $collection->getTable('ciextendedordergrid/order_item')
                ),
                'order_item.item_id = ' . $this->_orderTableAlias . '.item_id',
                $orderItemsColumns
            );
        }

        $collection->getSelect()->group('main_table.entity_id');
        if (count($excludeStatuses) > 0){
            $collection->getSelect()->where(
                $collection->getConnection()->quoteInto('main_table.status NOT IN (?)', $excludeStatuses)
            );
        }

//        $collection->setIsCustomerMode(TRUE);
        
    }
    
    protected function prepareGrid(&$grid, $export = FALSE){
        $orderItem = Mage::getModel("ciextendedordergrid/order_item");

        $mappedColumn = Mage::getModel("ciextendedordergrid/order_item")->getMappedColumns();
        $collection = $orderItem->getAttributes();

        $collection->getSelect()->where(
            $collection->getConnection()->quoteInto('main_table.attribute_code IN (?)', $mappedColumn)
        );

        $attributes = $collection->getItems();

        $showImages = Mage::getStoreConfig('ciextendedordergrid/general/images');
        
        $showShipping = Mage::getStoreConfig('ciextendedordergrid/general/shipping');
        $showPayment = Mage::getStoreConfig('ciextendedordergrid/general/payment');
        $showCoupon = Mage::getStoreConfig('ciextendedordergrid/general/coupon');
        $showCustomerEmail = Mage::getStoreConfig('ciextendedordergrid/general/customer_email');
        
        $showShippingAddress = Mage::getStoreConfig('ciextendedordergrid/general/shipping_address');
        $showBillingAddress = Mage::getStoreConfig('ciextendedordergrid/general/billing_address');
        
        if (intval($showImages) > 0 && !$export){
            
            $grid->addColumn('cmsideas_product_images', array(
                'header' => '',
                'index' => 'product_images',
                'renderer'  => 'ciextendedordergrid/adminhtml_sales_order_grid_renderer_images',
                'width' => 80,
                'filter' => false,
                'sortable'  => false,
            ));
        }
        
        if (intval($showCoupon) > 0){
            $grid->addColumn('cmsideas_coupon_code', array(
                'header' => Mage::helper('ciextendedordergrid')->__('Coupon Code'),
                'index' => 'cmsideas_coupon_code',
                'width' => 80,
                'filter_index' => 'order.coupon_code'
                
            ));
        }
        
        if (intval($showShipping) > 0){
            $grid->addColumn('cmsideas_shipping_description', array(
                'header' => Mage::helper('ciextendedordergrid')->__('Shipping Code'),
                'index' => 'cmsideas_shipping_description',
                'width' => 80,
                'filter_index' => 'order.shipping_description'
                
            ));
        }
        
        if (intval($showPayment) > 0){
            $grid->addColumn('cmsideas_method', array(
                'header' => Mage::helper('ciextendedordergrid')->__('Payment Method'),
                'renderer'  => 'ciextendedordergrid/adminhtml_sales_order_grid_renderer_payment',
                'index' => 'cmsideas_method',
                'width' => 80,
                'type'  => 'options',
                'options' => Mage::helper('payment')->getPaymentMethodList(),
                'filter_index' => 'order_payment.method'
            ));
            
        }
        
        if (intval($showShippingAddress) > 0){
            $grid->addColumn('cmsideas_shipping_address', array(
                'header' => Mage::helper('ciextendedordergrid')->__('Shipping Address'),
                'renderer'  => 'ciextendedordergrid/adminhtml_sales_order_grid_renderer_address_shipping',
                'index' => 'cmsideas_order_item_address_id',
                'width' => 80,
                'sortable'  => false,
                'filter_index' => 
                        ' CONCAT(shipping_order_address.country_id, 
                        shipping_order_address.region,
                        shipping_order_address.city,
                        shipping_order_address.street) ',
                    
            ));
        }
        
        if (intval($showBillingAddress) > 0){
            $grid->addColumn('cmsideas_billing_address', array(
                'header' => Mage::helper('ciextendedordergrid')->__('Billing Address'),
                'renderer'  => 'ciextendedordergrid/adminhtml_sales_order_grid_renderer_address_billing',
                'index' => 'cmsideas_order_item_address_id',
                'width' => 80,
                'sortable'  => false,
                'filter_index' => 
                        ' CONCAT(billing_order_address.country_id, 
                        billing_order_address.region,
                        billing_order_address.city,
                        billing_order_address.street) ',
            ));
        }
        
        if (intval($showCustomerEmail) > 0){
            $grid->addColumn('cmsideas_customer_email', array(
                'header' => Mage::helper('ciextendedordergrid')->__('Customer Email'),
                'index' => 'cmsideas_customer_email',
                'width' => 80,
                'filter_index' => 'order.customer_email'
                
            ));
        }
        
        $this->addDefaultColumns($grid);
        
        $after = 'status';
        foreach($attributes as $attribute){

            $column = $attribute->getAttributeCode();

            $grid->addColumnAfter($this->_extrOrderColumnPrefix.$column, array(
                'header' => $attribute->getFrontendLabel(),
                'index' => $this->_orderTableAlias.'.'.$column,
                'renderer'  => 'ciextendedordergrid/adminhtml_sales_order_grid_renderer_'.($export ? 'export' : 'default'),
//                'width' => '150px',
                'filter_index' => $this->_orderTableAlias.'.'.$column
            ), $after);

            $after = $this->_extrOrderColumnPrefix.$column;
        }
        
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $grid->addColumnAfter('cmsideas_action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'     => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base'=>'*/sales_order/view'),
                            'field'   => 'order_id'
                        )
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
            ), $after);
        }
    }
    
    protected function addDefaultColumns(&$grid){
        $grid->addColumn('cmsideas_real_order_id', array(
            'header'=> Mage::helper('sales')->__('Order #'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'increment_id',
            'filter_index' => 'main_table.increment_id'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $grid->addColumn('cmsideas_store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
            'filter_index' => 'main_table.store_id'
            ));
        }

        $grid->addColumn('cmsideas_created_at', array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
            'filter_index' => 'main_table.created_at'
        ));

        $grid->addColumn('cmsideas_billing_name', array(
            'header' => Mage::helper('sales')->__('Bill to Name'),
            'index' => 'billing_name',
            'filter_index' => 'main_table.billing_name'
        ));

        $grid->addColumn('cmsideas_shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
            'filter_index' => 'main_table.shipping_name'
        ));

        $grid->addColumn('cmsideas_base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
            'filter_index' => 'main_table.base_grand_total'
        ));

        $grid->addColumn('cmsideas_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
            'filter_index' => 'main_table.grand_total'
        ));

        $grid->addColumn('cmsideas_status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'filter_index' => 'main_table.status',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

//        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
//            $grid->addColumn('cmsideas_action',
//                array(
//                    'header'    => Mage::helper('sales')->__('Action'),
//                    'width'     => '50px',
//                    'type'      => 'action',
//                    'getter'     => 'getId',
//                    'actions'   => array(
//                        array(
//                            'caption' => Mage::helper('sales')->__('View'),
//                            'url'     => array('base'=>'*/sales_order/view'),
//                            'field'   => 'order_id'
//                        )
//                    ),
//                    'filter'    => false,
//                    'sortable'  => false,
//                    'index'     => 'stores',
//                    'is_system' => true,
//            ));
//        }
    }
    
    public function modifyOrderCollection($observer)
    {
        $collection = $observer->getCollection();
//        var_dump(get_class($collection));
        if ($collection instanceof Mage_Sales_Model_Mysql4_Order_Grid_Collection){

            $this->prepareOrderCollectionJoins($collection);
            $this->removeDefaultColumns();
        }
    }
    
    public function modifyOrderGridAfterBlockGenerate($observer){
        
        
        $permissibleActions = array('index', 'grid', 'exportCsv', 'exportExcel');
        $exportActions = array('exportCsv', 'exportExcel');
        
        if ( false === strpos(Mage::app()->getRequest()->getControllerName(), 'sales_order') || 
             !in_array(Mage::app()->getRequest()->getActionName(), $permissibleActions) ){
             
            return;
        }
        
        $block = $observer->getBlock();

        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_Grid || $block instanceof EM_DeleteOrder_Block_Adminhtml_Sales_Order_Grid){
            $this->prepareGrid($block, in_array(Mage::app()->getRequest()->getActionName(), $exportActions));   
        }
    }
    
    
    protected function removeDefaultColumns(){
        $mainTableColumns = array(
            'real_order_id', 'store_id',
            'created_at', 'billing_name', 'shipping_name', 'base_grand_total',
            'grand_total', 'status', 'action'
        );

        $layout = Mage::getSingleton('core/layout');
        $grid = $layout->getBlock('sales_order.grid');
        
        if ($grid){
            $columns = $grid->getColumns();

            foreach($columns as $index => $column){

                $columnId = $column->getId();
                if (in_array($columnId, $mainTableColumns))
                {
                    if (method_exists($grid, 'removeColumn'))
                        $grid->removeColumn($columnId);
                    else
                        $grid->addColumn($columnId, array(
                            'header_css_class' => 'cmsideas_hidden',
                            'column_css_class' => 'cmsideas_hidden',
                            'filter'    => false,
                            'sortable'  => false,
                        ));
                }
            }
        }
        
        
    }
}
?>