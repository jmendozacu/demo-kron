<?php

class Ebizmarts_BakerlooRestful_Model_Api_Orders extends Ebizmarts_BakerlooRestful_Model_Api_Api {

    protected $_model = "sales/order";

    public function checkDeletePermissions() {
        //Validate permissions
        $this->checkPermission(array('bakerloo_api/login', 'bakerloo_api/orders/delete'));
    }

    public function checkPostPermissions() {
        //Validate permissions
        $this->checkPermission(array('bakerloo_api/login', 'bakerloo_api/orders/create'));
    }

    public function getOrderOptions($item) {
        $result = array();
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (!empty($options['attributes_info'])) {
                $result = array_merge($options['attributes_info'], $result);
            }
        }

        $selections = array();
        foreach ($result as $option) {

            $_sel = array('label' => $option['label'], 'value' => '');

            if(!is_array($option['value']))
                $_sel['value'] = $option['value'];
            /*else
                //TODO*/


            array_push($selections, $_sel);
        }

        return $selections;
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
                    'qty'          => ($item->getQtyOrdered() * 1),
                    'qty_invoiced' => ($item->getQtyInvoiced() * 1),
                    'qty_shipped'  => ($item->getQtyShipped() * 1),
                    'qty_refunded' => ($item->getQtyRefunded() * 1),
                    'qty_canceled' => ($item->getQtyCanceled() * 1),
                    'price'        => (float)$item->getPrice(),
                    'tax_percent'  => (float)$item->getTaxPercent(),
                    'discount'     => (float)$item->getDiscountAmount(),
                    'options'      => $this->getOrderOptions($item)
                );

            }

            $shippingAddress = is_object($order->getShippingAddress()) ? $order->getShippingAddress() : new Varien_Object;
            $billingAddress  = is_object($order->getBillingAddress()) ? $order->getBillingAddress() : new Varien_Object;

            $result += array(
                            'entity_id'            => (int)$order->getId(),
                            'status'               => $order->getStatusLabel(),
                            'created_at'           => $order->getCreatedAt(),
                            'updated_at'           => $order->getUpdatedAt(),
                            "store_id"             => (int)$order->getStoreId(),
                            "store_name"           => $order->getStoreName(),
                            "customer_id"          => (int)$order->getCustomerId(),
                            "base_subtotal"        => (float)$order->getBaseSubtotal(),
                            "subtotal"             => (float)$order->getSubtotal(),
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
                            "customer_email"       => (string)$order->getCustomerEmail(),
                            "customer_firstname"   => (string)$order->getCustomerFirstname(),
                            "customer_lastname"    => (string)$order->getCustomerLastname(),
                            "shipping_name"        => (string)$shippingAddress->getName(),
                            "billing_name"         => (string)$billingAddress->getName(),
                            'products'             => $orderItems,
                            'invoices'             => $this->_getAssociatedData($order->getId(), 'invoices'),
                            'creditnotes'          => $this->_getAssociatedData($order->getId(), 'creditnotes')
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

            if($order->getId()) {
                $order = Mage::getModel('sales/order')->load($order->getId());
            }

            if($order->getId() && isset($data->comments)) {
                $order->addStatusHistoryComment(nl2br($data->comments), false)
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

                //If not Virtual, create shipment if indicated.
                if( !$order->getIsVirtual() ) {

                    $createShipment = false;

                    $paymentShip    = (int)$order->getPayment()->getMethodInstance()->getConfigData("ship");
                    $shippingMethod = explode('_', $order->getShippingMethod(), 3);

                    if( !isset($shippingMethod[2]) )
                        $shipmentShip = $paymentShip;
                    else
                        $shipmentShip = (int)Mage::getStoreConfig('carriers/' . $shippingMethod[2] . '/ship');

                    if( (1 === $paymentShip) || ((2 === $paymentShip) && (1 === $shipmentShip)) )
                        $createShipment = true;

                    if($createShipment) {
                        $shipment = Mage::getModel('sales/service_order', $invoice->getOrder())
                                                ->prepareShipment(array());
                        $shipment->register();
                        if ($shipment) {
                            $shipment->setEmailSent($invoice->getEmailSent());
                            $transactionSave->addObject($shipment);
                        }
                    }
                }

                $transactionSave->save();
            }

            $returnData['order_id']     = (int)$order->getId();
            $returnData['order_number'] = (int)$order->getIncrementId();
            $returnData['order_state']  = $order->getState();
            $returnData['order_status'] = $order->getStatusLabel();
            $returnData['order_data']   = $this->_createDataObject($order->getId());

            $posOrder->setFailMessage('')->save();

            //Inactivate quote.
            $service->getQuote()->save();

        }catch(Exception $e) {

            Mage::logException($e);

            $posOrderId = (int)$posOrder->getId();

            $message = $e->getMessage();

            $returnData['order_id']      = $posOrderId;
            $returnData['order_number']  = $posOrderId;
            $returnData['order_state']   = "notsaved";
            $returnData['order_status']  = "notsaved";
            $returnData['error_message'] = $message;

            $posOrder->setFailMessage($message)
                     ->setRequestUrl(Mage::helper('core/url')->getCurrentUrl())
                     ->save();

            Mage::helper('bakerloo_restful/sales')->notifyAdmin(array(
                    'severity'      => Mage_AdminNotification_Model_Inbox::SEVERITY_CRITICAL,
                    'date_added'    => Mage::getModel('core/date')->date(),
                    'title'         => Mage::helper('bakerloo_restful')->__("POS order number #%s failed.", $posOrderId),
                    'description'   => Mage::helper('bakerloo_restful')->__($message),
                    'url'           => null /*Mage::helper('adminhtml')->getUrl('adminhtml/bakerlooorders/', array('id' => $posOrderId))*/,
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

            if ($order->canCancel()) {
                $order->cancel()
                      ->save();
            }
            else {
                Mage::throwException("Order can not be canceled.");
            }
        }
        else {
            Mage::throwException("Order does not exist.");
        }

        return array(
            'order_id'     => (int)$order->getId(),
            'order_number' => (int)$order->getIncrementId(),
            'order_state'  => $order->getState(),
            'order_status' => $order->getStatusLabel()
        );
    }

    protected function _getCollection() {
        return Mage::getResourceModel('sales/order_collection');
    }

    /**
     * Applying array of filters to collection
     *
     * @param $filters
     */
    public function _applyFilters($filters, $useOR = false) {

        if(count($filters) == 1) {
            $filter = list($attributeCode, $condition, $value) = explode($this->_querySep, $filters[0]);

            if("increment_id" == $filter[0] && $filter[1] == 'eq') {

                //Value to filter by.
                $filterValue = $filter[2];

                $orderByIncrementId = Mage::getModel($this->_model)->loadByIncrementId($filterValue);
                if($orderByIncrementId->getId()) {
                    $this->_collection->getSelect()->where('main_table.increment_id = ?', $filterValue);
                }
                else {
                    $posOrder = Mage::getModel('bakerloo_restful/order')->getCollection()->addFieldToFilter('device_order_id', $filterValue)->getFirstItem();
                    if($posOrder->getId()) {
                        $this->_collection->getSelect()->joinLeft(
                                                            array('pos' => Mage::getSingleton('core/resource')->getTableName('bakerloo_restful/order')),
                                                            'main_table.entity_id = pos.order_id',
                                                            array()
                                                        )
                                                        ->where('pos.device_order_id = ?', $filterValue);
                    }
                    else {
                        $this->_collection->getSelect()->where('main_table.increment_id = ?', $filterValue);
                    }
                }

            }
            else {
                parent::_applyFilters($filters, true);
            }
        }
        else {
            parent::_applyFilters($filters, true);
        }

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

            //Check that order is not duplicate by order guid.
            if(isset($data->order_guid)) {
                $duplicate = Mage::getModel('bakerloo_restful/order')->load($data->order_guid, 'order_guid');
                if($duplicate->getId()) {
                    Mage::throwException("Duplicate POST for `{$data->order_guid}`.");
                }
            }

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

            $_rawData = json_decode($rawData);

            if(isset($_rawData->payment->customer_signature)) {
                $_bakerlooOrder->setCustomerSignature($_rawData->payment->customer_signature);

                unset($_rawData->payment->customer_signature);
            }

            $_bakerlooOrder->setJsonPayload(json_encode($_rawData));
        }

        //Device Order ID
        if(isset($data->internal_id)) {
            $_bakerlooOrder->setDeviceOrderId($data->internal_id);
        }

        if(isset($data->order_guid)) {
            $_bakerlooOrder->setOrderGuid($data->order_guid);
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
                            'subtotal',
                            'base_subtotal',
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

        Mage::app()->setCurrentStore($order->getStoreId());

        $result = array(
            'order_id'     => (int)$order->getId(),
            'order_number' => (int)$order->getIncrementId(),
            'order_state'  => $order->getState(),
            'order_status' => $order->getStatusLabel(),
            'email_sent'   => false
        );

        try {

            if($this->getRequest()->isPost()) {
                $data = $this->getJsonPayload();
            }

            $changedCustomer = false;

            //Allow recipient email to be other than the one associated to the order.
            $customEmail      = (string)$this->_getQueryParameter('email');
            $validCustomEmail = filter_var($customEmail, FILTER_VALIDATE_EMAIL);
            if ($validCustomEmail !== false) {
                $currentEmail = $order->getCustomerEmail();
                $order->setCustomerEmail($customEmail);

                $websiteId     = Mage::app()->getStore()->getWebsiteId();
                $customerExists = Mage::helper('bakerloo_restful/sales')->customerExists($customEmail, $websiteId);

                if(false === $customerExists) {
                    $name = substr($customEmail, 0, strpos($customEmail, '@'));

                    $customerData = new stdClass;
                    $customerData->customer = new stdClass;
                    $customerData->customer->group_id  = Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID, Mage::app()->getStore()->getId());
                    $customerData->customer->email     = $customEmail;
                    $customerData->customer->firstname = $name;
                    $customerData->customer->lastname  = $name;

                    $newCustomer = Mage::helper('bakerloo_restful')->createCustomer($websiteId, $customerData);
                    //@TODO: Add addresses if not equal to store.

                    $customerInOrderIsGuest = Mage::helper('bakerloo_restful/sales')->customerInOrderIsGuest($order);

                    //Associate customer to order.
                    if($newCustomer->getId() and $customerInOrderIsGuest) {
                        $order->setCustomer($newCustomer);
                        $order->setCustomerId($newCustomer->getId());
                        $order->setCustomerIsGuest(0);
                        $order->setCustomerEmail($newCustomer->getEmail());
                        $order->setCustomerFirstname($newCustomer->getFirstname());
                        $order->setCustomerLastname($newCustomer->getLastname());
                        $order->setCustomerGroupId($newCustomer->getGroupId());

                        $changedCustomer = true;
                        unset($currentEmail);
                    }
                }

            }

            $emailType = (string)Mage::helper('bakerloo_restful')->config('pos_receipt/receipts', $this->getStoreId());

            $emailSent = false;

            if(isset($data->attachments) and is_array($data->attachments) and !empty($data->attachments)) {

                if('magento' == $emailType) {
                    //send magento email

                    $order->sendNewOrderEmail();

                    $emailSent = (bool)$order->getEmailSent();
                }
                else {

                    $receiptData = current($data->attachments);

                    if('receipt' == $emailType) {
                        //send receipt email

                        $receipt = Mage::helper('bakerloo_restful/email')->sendReceipt($order, $receiptData);

                        $emailSent = (bool)$receipt->getEmailSent();
                    }
                    else { //Both
                        //send magento email and receipt

                        $order->sendNewOrderEmail();
                        $receipt = Mage::helper('bakerloo_restful/email')->sendReceipt($order, $receiptData);

                        $emailSent = (bool)($order->getEmailSent() or $receipt->getEmailSent());
                    }
                }

            }
            else {
                $order->sendNewOrderEmail();

                $emailSent = (bool)$order->getEmailSent();
            }

            $result['email_sent'] = $emailSent;


            //Subscribe email to newsletter if indicated
            $subscribeToNewsletter = (bool)$this->_getQueryParameter('subscribe_to_newsletter');
            if($emailSent and $subscribeToNewsletter) {

                $customer = Mage::getModel('customer/customer')
                                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                                ->loadByEmail($order->getCustomerEmail());
                if($customer->getId()) {
                    $customer->setIsSubscribed(1);
                    $customer->save();
                }
                else {
                    Mage::getModel('newsletter/subscriber')->subscribe($order->getCustomerEmail());
                }

            }


            //Reset order customer email if it was changed.
            //If order was placed with STORE email, change to custom email.
            if(isset($currentEmail)) {
                $storeEmail = (string)Mage::app()->getStore()->getConfig('trans_email/ident_general/email');

                if($storeEmail != $currentEmail) {
                    $order->setCustomerEmail($currentEmail);
                }

            }

            //Add comment to order.
            if($emailSent && $validCustomEmail !== false) {
                $order->addStatusHistoryComment(Mage::helper('bakerloo_restful')->__("Order email sent to custom email: \"%s\"", $customEmail), false)
                    ->setIsVisibleOnFront(false)
                    ->setIsCustomerNotified(false)
                    ->save();
            }

            //In case email is changed
            if(isset($currentEmail) or $changedCustomer) {
                $order->save();
            }

        } catch (Exception $e) {
            Mage::logException($e);

            $result['email_sent'] = false;
        }

        return $result;

    }

    /**
     * Search orders by POS order number.
     *
     * @return array|Varien_Object
     */
    public function searchByPosOrderId() {

        $id = (int)$this->_getQueryParameter('id');

        $collection = Mage::getModel('bakerloo_restful/order')->getCollection();
        $collection->addFieldToFilter('id', $id);

        $order = new Varien_Object;
        if($collection->getSize()) {
            $_order = $this->_createDataObject($collection->getFirstItem()->getOrderId());

            if(is_array($_order) and isset($_order['entity_id'])) {
                $order = $_order;
            }
        }

        return $order;

    }

    /**
     * Return ready to pickup orders.
     *
     * @return array
     */
    public function readyToPickup() {
        //get page
        $page = $this->_getQueryParameter('page');
        if(!$page) {
            $page = 1;
        }

        //Retrieve orders not completed and placed with our shipping method.
        $myFilters = array(
            'shipping_method,eq,bakerloo_store_pickup_bakerloo_store_pickup',
            'state,neq,complete',
            'total_paid,notnull,',
        );

        $filters = $this->_getQueryParameter('filters');

        if(is_null($filters)) {
            $filters = $myFilters;
        }
        else {
            $filters = array_merge($filters, $myFilters);
        }

        return $this->_getAllItems($page, $filters);
    }


    private function _getAssociatedData($orderId, $resource) {

        $api = Mage::getModel('bakerloo_restful/api_' . $resource);
        $api->parameters = array(
            'not_by_id'=>'not_by_id',
            'filters' => array('order_id,eq,' . $orderId)
        );

        $invoices = $api->get();

        if(is_array($invoices) and array_key_exists('page_data', $invoices)) {
            return $invoices['page_data'];
        }
        else {
            return array();
        }

    }

}