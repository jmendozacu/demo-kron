<?php

class Ebizmarts_BakerlooRestful_Adminhtml_BakerlooordersController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {

        $this->_title($this->__('Orders'))
             ->_title($this->__('POS'));

        $this->loadLayout();
        $this->_setActiveMenu('ebizmarts_pos');
        $this->renderLayout();
    }

    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('bakerloo_restful/adminhtml_bakerlooorders_grid')->toHtml()
        );
    }

    /**
     * Export data to CSV format
     */
    public function exportCsvAction() {
        $fileName = 'pos_orders.csv';
        $content = $this->getLayout()
                        ->createBlock('bakerloo_restful/adminhtml_bakerlooorders_grid')
                        ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function editAction() {

        $id    = $this->getRequest()->getParam('id');
        $model = Mage::getModel('bakerloo_restful/order');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError($this->__('This order no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $this->__("Editing order #%s", $model->getId()) : $this->__('New Order'));
        $this->_title($this->__('Orders'))
             ->_title($this->__('POS'));

        // Restore previously entered form data from session
        $data = $this->_getSession()->getUserData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('bakerlooorder', $model);

        $this->loadLayout();
        $this->_setActiveMenu('ebizmarts_pos');

        $this->renderLayout();

    }

    public function saveAction() {

        if($this->getRequest()->isPost()) {

            try {

                $postData = $this->getRequest()->getPost('order');

                $order = Mage::getModel('bakerloo_restful/order')->load((int)$postData['id']);

                if (!$order->getId()) {
                    $this->_getSession()->addError(Mage::helper('bakerloo_restful')->__('The order does not exist.'));
                }
                else {
                    try {
                        $order->addData($postData)->save();

                        $this->_getSession()->addSuccess(Mage::helper('bakerloo_restful')->__('The order has been saved.'));
                    } catch (Exception $e) {
                        $this->_getSession()->addError($e->getMessage());
                    }
                }

                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setUserData($postData);

                $this->_redirect('*/*/edit/');

                return;
            }
        }

        $this->_redirect('adminhtml/bakerlooorders/');
    }

    public function deleteAction() {
        $orderId = (int)$this->getRequest()->getParam('id');

        if ($orderId) {
            $order = Mage::getModel('bakerloo_restful/order')
                       ->load($orderId);
            try {
                $order->delete();
                $this->_getSession()->addSuccess($this->__('The order has been deleted.'));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }

        $this->_redirect('adminhtml/bakerlooorders/');

    }

    /**
     * Try to POST order again.
     */
    public function placeAction() {
        $orderId = (int)$this->getRequest()->getParam('id');

        if ($orderId) {

            $postData = $this->getRequest()->getPost('order', array());

            $order = Mage::getModel('bakerloo_restful/order')
                       ->load($orderId);

            if(!empty($postData)) {
                $order->addData($postData)->save();
            }

            if (!$order->getId()) {
                $this->_getSession()->addError(Mage::helper('bakerloo_restful')->__('The order does not exist.'));
            }
            else {
                try {

                    //Throw error if Magento order already exists
                    if($order->getOrderId()) {
                        Mage::throwException(Mage::helper('bakerloo_restful')->__('This order is already processed.'));
                    }

                    //POST
                    $headers = array();
                    $jsonHeaders = json_decode($order->getJsonRequestHeaders());

                    foreach(Mage::helper('bakerloo_restful')->allPossibleHeaders() as $_h) {
                        if(isset($jsonHeaders->{$_h})) {
                            array_push($headers, "$_h: {$jsonHeaders->{$_h}}");
                        }
                    }

                    array_push($headers, "B-Order-Id: {$order->getId()}");

                    $response = Mage::helper('bakerloo_restful/http')->POST($order->getRequestUrl(), $order->getJsonPayload(), $headers);

                    $objResponse = json_decode($response);

                    if(!is_object($objResponse)) {

                        $this->_getSession()->addError(Mage::helper('bakerloo_restful')->__('Could not process order, please try again. Response: %s', $response));

                    }
                    else {
                        if(isset($objResponse->error)) {
                            $message = $objResponse->error->message;
                            $this->_getSession()->addError(Mage::helper('bakerloo_restful')->__('Could not process order, please try again. Error: %s', $message));
                        }
                        else {

                            if((isset($objResponse->order_status) && $objResponse->order_status == "notsaved")
                                or !isset($objResponse->order_number)) {

                                if(isset($objResponse->error_message)) {
                                    $response = $objResponse->error_message;
                                }

                                $this->_getSession()->addError(Mage::helper('bakerloo_restful')->__('Could not save order, please try again. Error message: "%s"', $response));
                            }
                            else {
                                $this->_getSession()->addSuccess(Mage::helper('bakerloo_restful')->__('Order created correctly #%s', $objResponse->order_number));
                            }

                        }

                    }

                }
                catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }

                $this->_redirect('adminhtml/bakerlooorders/edit', array('id' => $orderId));
                return;
            }

        }

        $this->_redirect('adminhtml/bakerlooorders/');
    }

    protected function _isAllowed() {
        switch ($this->getRequest()->getActionName()) {
            case 'delete':
                $acl = 'ebizmarts_pos/orders/remove';
                break;
            case 'exportCsv':
                $acl = 'ebizmarts_pos/orders/export';
            case 'save':
            case 'edit':
                $acl = 'ebizmarts_pos/orders/edit';
                break;
            case 'place':
                $acl = 'ebizmarts_pos/orders/retry';
            break;
            default:
                $acl = 'ebizmarts_pos/orders/list';
        }
        return Mage::getSingleton('admin/session')->isAllowed($acl);
    }

}