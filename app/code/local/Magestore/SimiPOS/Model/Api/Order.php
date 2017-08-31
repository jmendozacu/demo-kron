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
 * Use to call api with prefix: order
 * Methods:
 *  list
 *  search
 *  info
 *  addComment
 *  hold
 *  unhold
 *  cancel
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Order extends Magestore_SimiPOS_Model_Api_Checkout_Abstract
{
    protected $_filtersMap = array(
        'order_id'      => 'entity_id',
    );
    
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
     * Retrieve POS order list
     * 
     * @param array|null $filters
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function apiList($filters = null, $page = null, $limit = null)
    {
        $collection = Mage::getResourceModel('sales/order_collection');
//        if ($this->isAdmin()) {
//            $collection->addFieldToFilter('simipos_user', array('gt' => 0));
//        } else {
//            $collection->addFieldToFilter('simipos_user', $this->getUser()->getId());
//        }
        if (!$this->isAdmin()) {
        	$permission = $this->getUser()->getPermission('order.list');
        	switch ($permission) {
        		case Magestore_SimiPOS_Model_Role::PERMISSION_OWNER:
        			$collection->addFieldToFilter('simipos_user', $this->getUser()->getId());
        			break;
        		case Magestore_SimiPOS_Model_Role::PERMISSION_OTHER:
        			$collection->addFieldToFilter('simipos_user', array('gt' => 0));
        	}
        }
        if (is_array($filters)) {
            foreach ($filters as $field => $value) {
                if (isset($this->_filtersMap[$field])) {
                    $field = $this->_filtersMap[$field];
                }
                $collection->addFieldToFilter($field, $value);
            }
        }
        if ($page > 0) {
            $collection->setCurPage($page);
        }
        if ($limit > 0) {
            $collection->setPageSize($limit);
        }
        $result = array(
            'total' => $collection->getSize()
        );
        foreach ($collection as $order) {
            $productSkus = array();
            foreach ($order->getAllVisibleItems() as $item) {
                $productSkus[] = $item->getSku();
            }
            $result[$order->getId()] = array(
                'increment_id'  => $order->getIncrementId(),
                'status'        => $order->getStatus(),
                'created_at'    => $order->getCreatedAt(),
                'customer_id'   => $order->getCustomerId(),
                'customer_name' => $order->getCustomerName(),
                'grand_total'   => $order->getGrandTotal(),
                'simipos_user'  => $order->getSimiposUser(),
                'simipos_email' => $order->getSimiposEmail(),
                'product_skus'  => implode(', ', $productSkus),
            );
        }
        return $result;
    }
    
    /**
     * Retrieve POS order list
     * 
     * @param string $searchTerm
     * @param int|null $page
     * @param int|null $limit
     * @return array
     */
    public function apiSearch($searchTerm = null, $page = null, $limit = null)
    {
        $collection = Mage::getResourceModel('sales/order_collection');
//        if ($this->isAdmin()) {
//            $collection->addFieldToFilter('simipos_user', array('gt' => 0));
//        } else {
//            $collection->addFieldToFilter('simipos_user', $this->getUser()->getId());
//        }
        if (!$this->isAdmin()) {
            $permission = $this->getUser()->getPermission('order.list');
            switch ($permission) {
                case Magestore_SimiPOS_Model_Role::PERMISSION_OWNER:
                    $collection->addFieldToFilter('simipos_user', $this->getUser()->getId());
                    break;
                case Magestore_SimiPOS_Model_Role::PERMISSION_OTHER:
                    $collection->addFieldToFilter('simipos_user', array('gt' => 0));
            }
        }
        $collection->getSelect()->order('created_at DESC');
        if ($searchTerm) {
            $searchTerm = "'%" . trim($searchTerm, '%') . "%'";
            $collection->getSelect()
                ->where("(increment_id LIKE $searchTerm) OR (customer_email LIKE $searchTerm) OR (customer_firstname LIKE $searchTerm) OR (customer_lastname LIKE $searchTerm) OR (grand_total LIKE $searchTerm)");
        }
        if ($page > 0) {
            $collection->setCurPage($page);
        }
        if ($limit > 0) {
            $collection->setPageSize($limit);
        }
        $result = array(
            'total' => $collection->getSize()
        );
        foreach ($collection as $order) {
        	$productSkus = array();
        	foreach ($order->getAllVisibleItems() as $item) {
        		$productSkus[] = $item->getSku();
        	}
            $result[$order->getId()] = array(
                'increment_id'  => $order->getIncrementId(),
                'status'        => $order->getStatus(),
                'created_at'    => $order->getCreatedAt(),
                'customer_id'   => $order->getCustomerId(),
                'customer_name' => $order->getCustomerName(),
                'grand_total'   => $order->getGrandTotal(),
                'simipos_user'  => $order->getSimiposUser(),
                'simipos_email' => $order->getSimiposEmail(),
                'product_skus'  => implode(', ', $productSkus),
            );
        }
        return $result;
    }
    
    public function apiInfo($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);
        
        $allowedAttributes = array(
            'increment_id', 'status', 'created_at', 'grand_total', 'store_id',
            'customer_id', 'customer_email', 'simipos_user', 'simipos_email', 'simipos_cash',
            'total_canceled', 'total_invoiced', 'total_refunded', 'total_paid', 'total_due',
        );
        $result = array();
        foreach ($order->getData() as $attribute => $value) {
            if (in_array($attribute, $allowedAttributes)) {
                $result[$attribute] = $value;
            }
        }
        $result['status_label'] = $order->getStatusLabel();
        $result['customer_name']= $order->getCustomerName();
        if ($order->getBillingAddress()) {
        	if ($order->getCustomerIsGuest()) {
        		$result['customer_name'] = $order->getBillingAddress()->getName();
        	}
        	// $result['customer_telephone'] = $order->getBillingAddress()->getTelephone();
        	// $result['customer_address'] = $order->getBillingAddress()->getFormated();
        }
        
        $result['items'] = $this->orderItems($order);
        
        $result['details'] = Mage::getModel('simipos/pdf_order')->getOrderData($order);
        
        try {
            $result['payment_method'] = $order->getPayment()->getMethod();
            $result['payment_method_title'] = $order->getPayment()->getMethodInstance()->getTitle();
            
            foreach ($order->getInvoiceCollection() as $invoice) {
            	if ($invoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_PAID) {
            		$result['invoice_id'] = $invoice->getIncrementId();
            	}
            }
//            $invoice = $order->getInvoiceCollection()->getFirstItem();
//            if ($invoice && $invoice->getId()) {
//            	$result['invoice_id'] = $invoice->getIncrementId();
//            }
        } catch (Exception $e) {
        }
        
        $result['history'] = array();
        foreach ($order->getAllStatusHistory() as $history) {
            $result['history'][$history->getId()] = array(
                'created_at'    => $history->getCreatedAt(),
                'comment'       => $history->getComment(),
                'status_label'  => $order->getConfig()->getStatusLabel($history->getStatus()),
            );
        }
        
        $result['totals'] = $this->orderTotals($order);
        return $result;
    }
    
    /**
     * Order Total Info
     * 
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function orderTotals($order)
    {
        $index = 1;
        $result = array(1 => array(
            'code'  => 'subtotal',
            'title' => $this->_helper->__('Subtotal'),
            'amount'=> $order->getSubtotal(),
        ));
        
        if ($order->getShippingAmount() > 0.0001) {
            $index++;
            $result[$index] = array(
                'code'  => 'shipping',
                'title' => $this->_helper->__('Shipping & Handling'),
                'amount'=> $order->getShippingAmount(),
            );
        }
        
        if ($order->getDiscountAmount() < -0.0001) {
            $index++;
            $result[$index] = array(
                'code'  => 'discount',
                'title' => $order->getDiscountDescription() ? $this->_helper->__('Discount (%s)', $order->getDiscountDescription()) : $this->_helper->__('Discount'),
                'amount'=> $order->getDiscountAmount(),
            );
        }
        
        if ($order->getTaxAmount() > 0.0001) {
            $index++;
            $result[$index] = array(
                'code'  => 'tax',
                'title' => $this->_helper->__('Tax'),
                'amount'=> $order->getTaxAmount(),
            );
        }
        
//        if ($order->getSimiposCash() > 0.0001) {
//        	$index++;
//        	$result[$index] = array(
//        	    'code'  => 'simipos_cash',
//        	    'title' => $this->_helper->__('Cash In'),
//        	    'amount'=> $order->getSimiposCash(),
//        	);
//        }
//        
        return $result;
    }
    
    /**
     * Order Items Info
     * 
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function orderItems($order)
    {
        $result = array();
        foreach ($order->getAllVisibleItems() as $item) {
            $result[$item->getId()] = $this->_getItemData($item);
            $result[$item->getId()]['qty_ordered'] = $item->getQtyOrdered();
            $result[$item->getId()]['qty_invoiced'] = $item->getQtyInvoiced();
            $result[$item->getId()]['qty_canceled'] = $item->getQtyCanceled();
            $result[$item->getId()]['qty_refunded'] = $item->getQtyRefunded();
            $result[$item->getId()]['qty_shipped'] = $item->getQtyShipped();
            // Product Data
            $product = Mage::getModel('catalog/product')->setStoreId($order->getStoreId());
            $product->load($item->getData('product_id'));
            $result[$item->getId()]['product_data'] = Mage::getSingleton('simipos/api_product')
                ->apiInfo($product);
            $result[$item->getId()]['image'] = Mage::helper('catalog/image')
                ->init($product, 'small_image')->resize(560, 440)->__toString();
            // Selected Option
            $infoRequest = $item->getProductOptionByCode('info_buyRequest');
            $result[$item->getId()]['selected_options'] = $this->_prepareOptions($infoRequest);
        }
        return $result;
    }
    
    /**
     * prepare selected options for item
     * 
     * @param array $options
     * @return array
     */
    protected function _prepareOptions($options)
    {
        $allowOptions = array(
            'options', 'bundle_option', 'super_attribute', 'super_group',
            'send_friend', 'recipient_name', 'recipient_email', 'recipient_ship', 'message', 'day_to_send'
        );
        $selectedOptions = array();
        foreach ($options as $code => $value) {
            if (!in_array($code, $allowOptions)) {
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $selectedOptions[$code . '[' . $subKey . ']'] = $subValue;
                }
            } else {
                $selectedOptions[$code] = $value;
            }
        }
        return $selectedOptions;
    }
    
    /**
     * add comment for order
     * 
     * @param string $orderIncrementId
     * @param string|null $comment
     * @param boolean $notify
     * @return boolean
     * @throws Exception
     */
    public function apiAddComment($orderIncrementId, $comment = null, $notify = false)
    {
        $order = $this->_initOrder($orderIncrementId);
        $order->addStatusToHistory($order->getStatus(), $comment, $notify);
        try {
            if ($notify && $comment) {
                $oldStore = Mage::getDesign()->getStore();
                $oldArea = Mage::getDesign()->getArea();
                Mage::getDesign()->setStore($order->getStoreId());
                Mage::getDesign()->setArea('frontend');
                
                $order->save();
                $order->sendOrderUpdateEmail($notify, $comment);
                
                Mage::getDesign()->setStore($oldStore);
                Mage::getDesign()->setArea($oldArea);
            } else {
                $order->save();
                $order->sendOrderUpdateEmail($notify, $comment);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 92);
        }
        return true;
    }
    
    /**
     * Hold order
     * 
     * @param string $orderIncrementId
     * @return boolean
     * @throws Exception
     */
    public function apiHold($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);
        try {
            $order->hold();
            $order->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 93); // status not changed
        }
        return true;
    }
    
    /**
     * Unhold order
     * 
     * @param string $orderIncrementId
     * @return boolean
     */
    public function apiUnhold($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);
        try {
            $order->unhold();
            $order->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 93);
        }
        return true;
    }
    
    public function apiCancel($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);
        if (Mage_Sales_Model_Order::STATE_CANCELED == $order->getState()) {
            throw new Exception($this->_helper->__('Order is canceled'), 93);
        }
        try {
            $order->cancel();
            $order->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 93);
        }
        if (Mage_Sales_Model_Order::STATE_CANCELED != $order->getState()) {
            throw new Exception($this->_helper->__('Cannot cancel this order'), 93);
        }
        return true;
    }
    
    public function apiEmail($orderIncrementId, $email = null)
    {
        $order = $this->_initOrder($orderIncrementId);
        if ($email == null) {
            $email = $order->getCustomerEmail();
        }
        
        $oldStore = Mage::getDesign()->getStore();
        $oldArea = Mage::getDesign()->getArea();
        $translate = Mage::getSingleton('core/translate');
        Mage::getDesign()->setStore($order->getStoreId());
        Mage::getDesign()->setArea('frontend');
        $translate->setTranslateInline(false);
        
        if ($order->getCustomerIsGuest()) {
            $template = Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_GUEST_TEMPLATE, $order->getStoreId());
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $template = Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_TEMPLATE, $order->getStoreId());
            $customerName = $order->getCustomerName();
        }
        $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true);
        $paymentBlock->getMethod()->setStore($order->getStoreId());
        Mage::getModel('core/email_template')
            ->setDesignConfig(array(
                'area'  => 'frontend',
                'store' => $order->getStoreId()
            ))->sendTransactional(
                $template,
                Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $order->getStoreId()),
                $email,
                $customerName,
                array(
                    'order' => $order,
                    'billing'   => $order->getBillingAddress(),
                    'payment_html'  => $paymentBlock->toHtml(),
                )
            );
        
        Mage::getDesign()->setStore($oldStore);
        Mage::getDesign()->setArea($oldArea);
        $translate->setTranslateInline(true);
        return true;
    }
    
    /**
     * Print order data
     * 
     * @param $orderIncrementId
     * @return array
     */
    public function apiPrint($orderIncrementId)
    {
    	$order = $this->_initOrder($orderIncrementId);
    	return Mage::getModel('simipos/pdf_order')->getPdfData($order);
    }
}
