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
 * SimiPOS Category API Model
 * Use to call api with prefix: order_creditmemo
 * Methods:
 *  list
 *  info
 *  
 *  create
 *  
 *  addComment
 *  cancel
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Order_Creditmemo extends Magestore_SimiPOS_Model_Api_Abstract
{
    /**
     * init order model
     * 
     * @param string $orderIncrementId
     * @return Mage_Sales_Model_Order
     * @throws Exception
     */
    protected function _initOrder($orderIncrementId)
    {
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($orderIncrementId);
        if (!$order->getId()) {
            throw new Exception($this->_helper->__('Order is not found.'), 91);
        }
        return $order;
    }
    
    public function apiCreate($orderIncrementId, $creditmemoData = null, $comment = null, $notifyCustomer = false, $includeComment = false)
    {
        $order = $this->_initOrder($orderIncrementId);
        if (!$order->canCreditmemo()) {
            throw new Exception($this->_helper->__('Cannot refund this order.'), 98);
        }
        $creditmemoData = $this->_prepareCreateData($creditmemoData);
        
        $service = Mage::getModel('sales/service_order', $order);
        if (isset($creditmemoData['do_offline']) && !$creditmemoData['do_offline']) {
            // Refund Online (need load invoice)
            foreach ($order->getInvoiceCollection() as $invoice) {
                if ($invoice->canRefund()) {
                    break;
                }
            }
            if (!isset($invoice) || !$invoice->canRefund()) {
                throw new Exception($this->_helper->__('Cannot make online refund for this order.'), 98);
            }
            $creditmemo = $service->prepareInvoiceCreditmemo($invoice, $creditmemoData);
            $creditmemo->setOfflineRequested(false);
        } else {
        	// Refund offline
        	$creditmemo = $service->prepareCreditmemo($creditmemoData);
        	$creditmemo->setOfflineRequested(true);
        }
        // Process back to stock flags
        $backToStock = array();
        if (isset($creditmemoData['stocks']) && is_array($creditmemoData['stocks'])) {
        	$backToStock = $creditmemoData['stocks'];
        }
        foreach ($creditmemo->getAllItems() as $creditmemoItem) {
        	$orderItem = $creditmemoItem->getOrderItem();
        	$parentId = $orderItem->getParentItemId();
        	if (isset($backToStock[$orderItem->getId()]) && $backToStock[$orderItem->getId()]) {
                $creditmemoItem->setBackToStock(true);
            } elseif ($orderItem->getParentItem() && isset($backToStock[$parentId]) && $backToStock[$parentId]) {
                $creditmemoItem->setBackToStock(true);
            } else {
                $creditmemoItem->setBackToStock(false);
            }
        }
        // $creditmemo->setPaymentRefundDisallowed(true)->register();
        $creditmemo->register();
        if (!empty($comment)) {
            $creditmemo->addComment($comment, $notifyCustomer);
        }
        try {
            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($creditmemo)
                ->addObject($order);
            if ($creditmemo->getInvoice()) {
                $transactionSave->addObject($creditmemo->getInvoice());
            }
            $transactionSave->save();
            $creditmemo->sendEmail($notifyCustomer, ($includeComment ? $comment : ''));
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 99);
        }
        return $creditmemo->getIncrementId();
    }
    
    /**
     * Prepare Data for Create Credit Memo
     *  qtys    => array (order_item_id => qty)     | qtys[id] => qty
     *  shipping_amount
     *  adjustment_positive + add amount to total
     *  adjustment_negative - remove amount to total
     * 
     * @param array $data
     * @return array
     */
    protected function _prepareCreateData($data)
    {
        $data = isset($data) ? $data : array();
        $data = $this->_helper->prepareData($data);
        if (isset($data['qtys']) && count($data['qtys'])) {
            $qtysArray = array();
            foreach ($data['qtys'] as $qKey => $qVal) {
                // Save backward compatibility
                if (is_array($qVal)) {
                    if (isset($qVal['order_item_id']) && isset($qVal['qty'])) {
                        $qtysArray[$qVal['order_item_id']] = $qVal['qty'];
                    }
                } else {
                    $qtysArray[$qKey] = $qVal;
                }
            }
            $data['qtys'] = $qtysArray;
        }
        return $data;
    }
}
