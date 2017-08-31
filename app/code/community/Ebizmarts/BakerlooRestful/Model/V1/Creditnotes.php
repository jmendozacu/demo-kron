<?php

class Ebizmarts_BakerlooRestful_Model_V1_Creditnotes extends Ebizmarts_BakerlooRestful_Model_V1_Api {

    protected $_model = "sales/order_creditmemo";

    public function checkPostPermissions() {
        //Validate permissions
        $this->checkPermission(array('bakerloo_api/login', 'bakerloo_api/orders/creditnote'));
    }

    public function _createDataObject($id = null, $data = null) {
        $result = null;

        if(is_null($data)) {
            $creditmemo = Mage::getModel($this->_model)->load($id);
        }
        else {
            $creditmemo = $data;
        }

        if($creditmemo->getId()) {

            $invoiceItems = array();

            foreach($creditmemo->getItemsCollection() as $item) {

                $invoiceItems[]= array(
                    'product_id' => (int)$item->getProductId(),
                    'qty'        => (int)($item->getQty() * 1),
                    'price'      => (float)$item->getPrice(),
                    'name'       => $item->getName(),
                    'sku'        => $item->getSku(),
                );

            }

            $result = array(
                            "entity_id"            => (int)$creditmemo->getId(),
                            "increment_id"         => (int)$creditmemo->getIncrementId(),
                            "state"                => $creditmemo->getStateName(),
                            "created_at"           => $creditmemo->getCreatedAt(),
                            "updated_at"           => $creditmemo->getUpdatedAt(),
                            "store_id"             => (int)$creditmemo->getStoreId(),
                            "base_grand_total"     => (float)$creditmemo->getBaseGrandTotal(),
                            "base_total_paid"      => (float)$creditmemo->getBaseTotalPaid(),
                            "grand_total"          => (float)$creditmemo->getGrandTotal(),
                            "total_paid"           => (float)$creditmemo->getTotalPaid(),
                            "tax_amount"           => (float)$creditmemo->getTaxAmount(),
                            "products"             => $invoiceItems,
            );
        }

        return $result;
    }

    /**
     * Create credit note in Magento.
     *
     */
    public function post() {
        parent::post();

        if(!$this->getStoreId()) {
            Mage::throwException('Please provide a Store ID.');
        }

        Mage::app()->setCurrentStore($this->getStoreId());

        $data    = $this->getJsonPayload();
        $orderId = (int)$data->order_id;
        $order   = Mage::getModel('sales/order')->load($orderId);
        //$invoice = $this->_initInvoice($order);
        $invoice = false;

        /**
         * Check order existing
         */
        if (!$order->getId()) {
            Mage::throwException(Mage::helper('bakerloo_restful')->__('The order does not exist.'));
        }

        /**
         * Check creditmemo create availability
         */
        if (!$order->canCreditmemo()) {
            Mage::throwException(Mage::helper('bakerloo_restful')->__('Cannot create credit memo for the order.'));
        }

        if (isset($data->items)) {
            $savedData = $data->items;
        }
        else {
            $savedData = array();
        }

        $qtys = array();
        $backToStock = array();
        foreach ($savedData as $itemData) {
            if (isset($itemData->qty)) {
                $qtys[$itemData->id] = $itemData->qty;
            }
            if (isset($itemData->back_to_stock)) {
                $backToStock[$itemData->id] = true;
            }
        }

    /*  'do_offline' => string '0' (length=1)
      'comment_text' => string '' (length=0)
      'shipping_amount' => string '0' (length=1)
      'adjustment_positive' => string '0' (length=1)
      'adjustment_negative' => string '0' (length=1)*/

        $creditMemoData = array();

        $memoItems = array();
        foreach($data->items as $_item) {
            $memoItems[$_item->id] = array('qty' => $_item->qty);
            if(isset($_item->back_to_stock)) {
                $memoItems[$_item->id]['back_to_stock'] = $_item->back_to_stock;
            }
        }

        $creditMemoData['items'] = $memoItems;
        $creditMemoData['qtys']  = $qtys;

        $service = Mage::getModel('sales/service_order', $order);
        if ($invoice) {
            $creditmemo = $service->prepareInvoiceCreditmemo($invoice, $creditMemoData);
        }
        else {
            $creditmemo = $service->prepareCreditmemo($creditMemoData);
        }

        /**
         * Process back to stock flags
         */
        foreach ($creditmemo->getAllItems() as $creditmemoItem) {
            $orderItem = $creditmemoItem->getOrderItem();
            $parentId  = $orderItem->getParentItemId();
            if (isset($backToStock[$orderItem->getId()])) {
                $creditmemoItem->setBackToStock(true);
            } elseif ($orderItem->getParentItem() && isset($backToStock[$parentId]) && $backToStock[$parentId]) {
                $creditmemoItem->setBackToStock(true);
            } elseif (empty($savedData)) {
                $creditmemoItem->setBackToStock(Mage::helper('cataloginventory')->isAutoReturnEnabled());
            } else {
                $creditmemoItem->setBackToStock(false);
            }
        }

        $args = array('creditmemo' => $creditmemo, 'request' => $this->getRequest());
        Mage::dispatchEvent('adminhtml_sales_order_creditmemo_register_before', $args);

        if (!empty($data->comment_text)) {
            Mage::getSingleton('adminhtml/session')->setCommentText($data->comment_text);
        }

        if ($creditmemo) {
            if (($creditmemo->getGrandTotal() <= 0) && (!$creditmemo->getAllowZeroGrandTotal())) {
                Mage::throwException(Mage::helper('bakerloo_restful')->__('Credit memo\'s total must be positive.'));
            }

            $comment = '';
            if (!empty($data->comment_text)) {
                $creditmemo->addComment($data->comment_text, isset($data->comment_customer_notify), isset($data->is_visible_on_front));
                if (isset($data->comment_customer_notify)) {
                    $comment = $data->comment_text;
                }
            }

            /*if (isset($data['do_refund'])) {
                $creditmemo->setRefundRequested(true);
            }*/
            //if (isset($data['do_offline'])) {
                $creditmemo->setOfflineRequested(true);
            //}

            $creditmemo->register();
            if (!empty($data->send_email)) {
                $creditmemo->setEmailSent(true);
            }

            $creditmemo->getOrder()->setCustomerNoteNotify(!empty($data->send_email));
            $this->_saveCreditmemo($creditmemo);
            $creditmemo->sendEmail(!empty($data->send_email), $comment);
            Mage::getSingleton('adminhtml/session')->getCommentText(true);

            return array(
                     'creditmemo_id'     => (int)$creditmemo->getEntityId(),
                     'creditmemo_number' => (int)$creditmemo->getIncrementId(),
                     'order_id'          => (int)$creditmemo->getOrderId(),
                    );

        }

        Mage::throwException(Mage::helper('bakerloo_restful')->__('Inavalid Credit Memo.'));

    }

    /**
     * Save creditmemo and related order, invoice in one transaction
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     */
    protected function _saveCreditmemo($creditmemo) {
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($creditmemo)
            ->addObject($creditmemo->getOrder());
        if ($creditmemo->getInvoice()) {
            $transactionSave->addObject($creditmemo->getInvoice());
        }
        $transactionSave->save();

        return $this;
    }

}