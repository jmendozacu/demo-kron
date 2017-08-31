<?php

class Magestore_Madapter_Block_Zooz extends Mage_Payment_Block_Info_Cc {

    protected $_tranS;

    protected function _prepareSpecificInformation($transport = null) {
        $orderId = Mage::app()->getRequest()->getParam('order_id');
        $invoiceId = Mage::app()->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
            $orderId = $invoice->getOrderId();
        } elseif (Mage::getSingleton('core/session')->getOrderIdForEmail()) {
            $orderId = Mage::getSingleton('core/session')->getOrderIdForEmail();
        }
        $train = Mage::getModel('madapter/madapter')->getCollection()
                ->addFieldToFilter('order_id', $orderId)
                ->getLastItem();
        $this->_tranS = $train;
        $info = null;
        $transport = parent::_prepareSpecificInformation($transport);
        if (count($train->getData())) {
            $info = array('Transaction ID' => $train->getTransactionId(),
                'Four Digits' => $train->getTransactionDis());
        } else {
            $info = array('Notice' => 'Pending');
        }

        return $transport->addData($info);
    }

    public function getCcTypeName() {
        return $this->_tranS->getTransactionName();
    }

}
