<?php

class Ebizmarts_BakerlooRestful_Model_V1_Orders extends Ebizmarts_BakerlooRestful_Model_V1_Api {

    protected $_model = "sales/order";

    public function checkDeletePermissions() {
        //Validate permissions
        $this->checkPermission(array('bakerloo_api/login', 'bakerloo_api/orders/delete'));
    }

    public function checkPostPermissions() {
        //Validate permissions
        $this->checkPermission(array('bakerloo_api/login', 'bakerloo_api/orders/create'));
    }

    public function _createDataObject($id = null, $data = null) {
        $result = array();

        $order = Mage::getModel($this->_model)->load($id);

        if($order->getId()) {

            $orderItems = array();

            foreach($order->getItemsCollection() as $item) {

                if($item->getParentItem()) {
                    continue;
                }

                $orderItems []= array(
                    'name'         => $item->getName(),
                    'sku'          => $item->getSku(),
                    'product_id'   => (int)$item->getProductId(),
                    'item_id'      => (int)$item->getItemId(),
                    'product_type' => $item->getProductType(),
                    'qty'          => (int)($item->getQtyOrdered() * 1),
                    'qty_invoiced' => (int)($item->getQtyInvoiced() * 1),
                    'qty_shipped'  => (int)($item->getQtyShipped() * 1),
                    'qty_refunded' => (int)($item->getQtyRefunded() * 1),
                    'qty_canceled' => (int)($item->getQtyCanceled() * 1),
                    'price'        => (float)$item->getPrice(),
                    'discount'     => (float)$item->getDiscountAmount(),
                );

            }

            $result += array(
                            'entity_id'            => (int)$order->getId(),
                            'status'               => $order->getStatus(),
                            'created_at'           => $order->getCreatedAt(),
                            'updated_at'           => $order->getUpdatedAt(),
                            "store_id"             => (int)$order->getStoreId(),
                            "store_name"           => $order->getStoreName(),
                            "customer_id"          => (int)$order->getCustomerId(),
                            "base_grand_total"     => (float)$order->getBaseGrandTotal(),
                            "base_total_paid"      => (float)$order->getBaseTotalPaid(),
                            "grand_total"          => (float)$order->getGrandTotal(),
                            "total_paid"           => (float)$order->getTotalPaid(),
                            "tax_amount"           => (float)$order->getTaxAmount(),
                            "discount_amount"      => (float)$order->getDiscountAmount(),
                            "coupon_code"          => (string)$order->getCouponCode(),
                            "shipping_description" => (string)$order->getShippingDescription(),
                            "shipping_amount"      => (float)$order->getShippingAmount(),
                            "increment_id"         => (int)$order->getIncrementId(),
                            "base_currency_code"   => $order->getBaseCurrencyCode(),
                            "order_currency_code"  => $order->getOrderCurrencyCode(),
                            "shipping_name"        => (string)$order->getShippingName(),
                            "billing_name"         => (string)$order->getBillingName(),
                            'products'             => $orderItems,
                            );
        }

        return $result;
    }

    /**
     * Create order in Magento.
     *
     */
    public function post() {

        parent::post();

        if(!$this->getStoreId()) {
            Mage::throwException('Please provide a Store ID.');
        }

        Mage::app()->setCurrentStore($this->getStoreId());

        $data = $this->getJsonPayload();

        $order = new Varien_Object;

        //Save order data to local storage
        $posOrder = $this->_saveOrder(null, $order, $data, $this->getRequest()->getRawBody());

        $returnData = array(
             'order_id'     => null,
             'order_number' => null,
             'order_state'  => "",
             'order_status' => ""
        );

        try {

            $quote = Mage::helper('bakerloo_restful/sales')->buildQuote($this->getStoreId(), $data);

            $service = Mage::getModel('sales/service_quote', $quote);

            $service->submitAll();

            $order = $service->getOrder();

            if($order->getId() && isset($data->comments)) {
                $order->addStatusHistoryComment($data->comments, false)
                    ->setIsVisibleOnFront(false)
                    ->setIsCustomerNotified(false)
                    ->save();
            }

            //Cancel order if its posted as canceled from device
            if(isset($data->order_state) && ((int)$data->order_state === 4)) {
                $order->cancel()
                    ->save();
            }

            //Invoice and ship
            if($order->getId() && $order->canInvoice()
                    && (1 === (int)$order->getPayment()->getMethodInstance()->getConfigData("invoice"))) {

                $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();

                $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);

                $invoice->setTransactionId(time());

                $invoice->register();

                //Do no send invoice email
                $invoice->setEmailSent(false);
                $invoice->getOrder()->setCustomerNoteNotify(false);

                $transactionSave = Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder());

                $createShipment = (1 === (int)$order->getPayment()->getMethodInstance()->getConfigData("ship"));
                if($createShipment) {
                    $shipment = Mage::getModel('sales/service_order', $invoice->getOrder())
                                            ->prepareShipment(array());
                    $shipment->register();
                    if ($shipment) {
                        $shipment->setEmailSent($invoice->getEmailSent());
                        $transactionSave->addObject($shipment);
                    }
                }

                $transactionSave->save();
            }

            $returnData['order_id']     = (int)$order->getId();
            $returnData['order_number'] = (int)$order->getIncrementId();
            $returnData['order_state']  = $order->getState();
            $returnData['order_status'] = $order->getStatus();

            $posOrder->setFailMessage('')->save();

        }catch(Exception $e) {

            $posOrderId = (int)$posOrder->getId();

            $returnData['order_id']     = $posOrderId;
            $returnData['order_number'] = $posOrderId;
            $returnData['order_state']  = "notsaved";
            $returnData['order_status'] = "notsaved";

            $message = $e->getMessage();
            $posOrder->setFailMessage($message)
                     ->setRequestUrl(Mage::helper('core/url')->getCurrentUrl())
                     ->save();

            Mage::helper('bakerloo_restful/sales')->notifyAdmin(array(
                    'severity'      => Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL,
                    'date_added'    => Mage::getModel('core/date')->date(),
                    'title'         => Mage::helper('bakerloo_restful')->__("POS order number #%s failed.", $posOrderId),
                    'description'   => Mage::helper('bakerloo_restful')->__($message),
                    'url'           => Mage::helper('adminhtml')->getUrl('adminhtml/bakerlooorders/', array('id' => $posOrderId)),
            ));
        }

        $this->_saveOrder($posOrder->getId(), $order, $data);

        return $returnData;

    }

    /**
     * Cancel order
     */
    public function delete() {
        parent::delete();

        $orderId = $this->_getIdentifier();

        $order = Mage::getModel('sales/order')->load($orderId);

        if($order->getId()) {
            $order->cancel()
              ->save();
        }
        else {
            Mage::throwException("Order does not exist.");
        }

        return array(
            'order_id'     => (int)$order->getId(),
            'order_number' => (int)$order->getIncrementId(),
            'order_state'  => $order->getState(),
            'order_status' => $order->getStatus()
        );
    }

    protected function _getCollection() {
        return Mage::getResourceModel('sales/order_grid_collection');
    }

    /**
     * Applying array of filters to collection
     *
     * @param $filters
     */
    public function _applyFilters($filters) {

        if(count($filters) == 1) {
            $filter = list($attributeCode, $condition, $value) = explode($this->_querySep, $filters[0]);

            if("increment_id" == $filter[0]) {
                //Search by IncrementID on orders table, JOIN with our table and also search by device_order_id
                $this->_collection->getSelect()
                ->joinLeft(
                    array('pos' => Mage::getSingleton('core/resource')->getTableName('bakerloo_restful/order')),
                    'main_table.entity_id = pos.order_id',
                    array()
                )
                ->where('pos.device_order_id = ?', $filter[2])
                ->orWhere('main_table.increment_id = ?', $filter[2]);
            }
        }

        return parent::_applyFilters($filters);
    }

    /**
     * Save order in local table POS > Orders.
     *
     * @param  int   $id      [description]
     * @param  Mage_Sales_Model_Order   $order   [description]
     * @param  stdClass $data    [description]
     * @param  string   $rawData [description]
     * @return Ebizmarts_BakerlooRestful_Model_Order            [description]
     */
    protected function _saveOrder($id = null, $order, stdClass $data, $rawData = null) {

        $_bakerlooOrder = Mage::getModel('bakerloo_restful/order');

        $headerId = (int)$this->_getRequestHeader('B-Order-Id');
        if($headerId) {
            $id = $headerId;
        }

        if(!is_null($id)) {
            $_bakerlooOrder->load($id);
        }
        else {
            //Store request headers in local table first time
            //so if it fails we can retry with all original data
            $requestHeaders = array();
            foreach(Mage::helper('bakerloo_restful')->allPossibleHeaders() as $_rqh) {
                $value = (string)$this->_getRequestHeader($_rqh);

                if(!empty($value)) {
                    $requestHeaders[$_rqh] = $value;
                }
            }

            $_bakerlooOrder->setJsonRequestHeaders(json_encode($requestHeaders));

        }

        //Save order in custom table
        $_bakerlooOrder
            ->setOrderIncrementId($order->getIncrementId())
            ->setOrderId($order->getId())
            ->setAdminUser($this->getUsername())
            ->setRemoteIp(Mage::helper('core/http')->getRemoteAddr())
            ->setDeviceId($this->getDeviceId())
            ->setUserAgent($this->getUserAgent());

        if(!is_null($rawData)) {
            $_bakerlooOrder->setJsonPayload($rawData);
        }

        //Device Order ID
        if(isset($data->internal_id)) {
            $_bakerlooOrder->setDeviceOrderId($data->internal_id);
        }

        if($this->getUsernameAuth()) {
            $_bakerlooOrder->setAdminUserAuth($this->getUsernameAuth());
        }

        if($this->getLatitude()) {
            $_bakerlooOrder->setLatitude($this->getLatitude());
        }
        if($this->getLongitude()) {
            $_bakerlooOrder->setLongitude($this->getLongitude());
        }

        //Store additional data.
        $additional = array(
                            'store_id',
                            'grand_total',
                            'base_grand_total',
                            'base_shipping_amount',
                            'base_tax_amount',
                            'base_to_global_rate',
                            'base_to_order_rate',
                            'base_currency_code',
                            'tax_amount',
                            'store_to_base_rate',
                            'store_to_order_rate',
                            'global_currency_code',
                            'order_currency_code',
                            'store_currency_code',
                      );

        foreach($additional as $_attribute) {
            $_bakerlooOrder->setData($_attribute, $order->getData($_attribute));
        }

        $_bakerlooOrder->save();

        return $_bakerlooOrder;
    }

    /**
     * Given an order ID, send order email.
     *
     * @return array Email sending result
     */
    public function sendEmail() {

        $orderId = (int)$this->_getQueryParameter('orderId');

        //Load order and check if exists.
        $order = Mage::getModel('sales/order')->load($orderId);

        if(!$order->getId()) {
            Mage::throwException("Order does not exist.");
        }

        $result = array(
            'order_id'     => (int)$order->getId(),
            'order_number' => (int)$order->getIncrementId(),
            'order_state'  => $order->getState(),
            'order_status' => $order->getStatus(),
            'email_sent'   => false
        );

        try {
            $order->sendNewOrderEmail();
        } catch (Exception $e) {
            Mage::logException($e);
        }

        $result['email_sent'] = (bool)$order->getEmailSent();

        return $result;

    }

}