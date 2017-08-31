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
 * Use to call api with prefix: order_invoice
 * Methods:
 *  list
 *  info
 *  
 *  create
 *  capture
 *  signature
 *  
 *  addComment
 *  capture
 *  void
 *  cancel
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Order_Invoice extends Magestore_SimiPOS_Model_Api_Abstract
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
    
    /**
     * Create Invoice for order
     * 
     * @param string $orderIncrementId
     * @param array $itemsQty
     * @param string $comment
     * @param boolean $email
     * @param boolean $includeComment
     * @return string
     * @throws Exception
     */
    public function apiCreate($orderIncrementId, $itemsQty = array(), $comment = null, $email = false, $includeComment = false)
    {
        $order = $this->_initOrder($orderIncrementId);
        if (!$order->canInvoice()) {
            throw new Exception($this->_helper->__('Cannot invoice this order'), 94);
        }
        
        $invoice = $order->prepareInvoice($this->_helper->prepareData($itemsQty));
        $invoice->register();
        
        if ($comment !== null) {
            $invoice->addComment($comment, $email);
        }
        if ($email) {
            $invoice->setEmailSent(true);
        }
        
        $invoice->getOrder()->setIsInProcess(true);
        try {
            Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
            $invoice->sendEmail($email, ($includeComment ? $comment : ''));
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 95);
        }
        return $invoice->getIncrementId();
    }
    
    /**
     * Capture Invoice
     * 
     * @param string $invoiceIncrementId
     * @return boolean
     * @throws Exception
     */
    public function apiCapture($invoiceIncrementId)
    {
        $invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceIncrementId);
        if (!$invoice->getId()) {
            throw new Exception($this->_helper->__('Invoice is not found.'), 96);
        }
        if (!$invoice->canCapture()) {
            throw new Exception($this->_helper->__('Invoice cannot be captured.'), 97);
        }
        try {
            $invoice->capture();
            $invoice->getOrder()->setIsInProcess(true);
            Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 93);
        }
        return true;
    }
    
    /**
     * Add Signature and capture invoice
     * 
     * @param string $invoiceIncrementId
     * @return boolean
     * @throws Exception
     */
    public function apiSignature($invoiceIncrementId)
    {
        $invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceIncrementId);
        if (!$invoice->getId()) {
            throw new Exception($this->_helper->__('Invoice is not found.'), 96);
        }
        // Upload Signature Image
        if (isset($_FILES['signature']['name']) && $_FILES['signature']['name'] != '') {
            try {
                $uploader = new Varien_File_Uploader('signature');
                
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                
                $path = Mage::getBaseDir('media') . DS  . 'simipos' . DS;
                $uploader->save($path, $invoice->getIncrementId() . '.png');
            } catch (Exception $e) {
                // Do nothing
            }
        }
        if (!$invoice->canCapture()) {
            return false;
        }
        try {
            $invoice->capture();
            $invoice->getOrder()->setIsInProcess(true);
            Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 93);
        }
        return true;
    }
}
