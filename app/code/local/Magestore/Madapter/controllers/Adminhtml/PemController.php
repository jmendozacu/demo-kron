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
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Madapter Adminhtml Controller
 * 
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @author  	Magestore Developer
 */
class Magestore_Madapter_Adminhtml_PemController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Madapter_Adminhtml_MadapterController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('madapter/pem')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction() {
        // $id = $this->getRequest()->getParam('id');
        // $model = Mage::getModel('madapter/madapter')->load($id);

        // if ($model->getId() || $id == 0) {
            // $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            // if (!empty($data))
                // $model->setData($data);

            // Mage::register('madapter_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('madapter/madapter');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('madapter/adminhtml_pem_edit'))
                    ->_addLeft($this->getLayout()->createBlock('madapter/adminhtml_pem_edit_tabs'));

            $this->renderLayout();
        // } else {
            // Mage::getSingleton('adminhtml/session')->addError(Mage::helper('madapter')->__('Item does not exist'));
            // $this->_redirect('*/*/');
        // }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    /**
     * save item action
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('filename');

                    // Any extention would work
                  
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
					$_FILES['filename']['name'] = Mage::helper('madapter')->getConfigNotice('name');
                    $path = Mage::getBaseDir('media') . DS . 'madapter'. DS;
                    $result = $uploader->save($path, $_FILES['filename']['name']);
                    $data['filename'] = $result['file'];
                } catch (Exception $e) {
                    $data['filename'] = $_FILES['filename']['name'];
                }
            }            

            // try {
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('madapter')->__('PEM file was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit');
                    return;
                // }
                // $this->_redirect('*/*/');
                return;           
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('madapter')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('madapter/madapter');
                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction() {
        $madapterIds = $this->getRequest()->getParam('madapter');
        if (!is_array($madapterIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($madapterIds as $madapterId) {
                    $madapter = Mage::getModel('madapter/madapter')->load($madapterId);
                    $madapter->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($madapterIds)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass change status for item(s) action
     */
    public function massStatusAction() {
        $madapterIds = $this->getRequest()->getParam('madapter');
        if (!is_array($madapterIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($madapterIds as $madapterId) {
                    $madapter = Mage::getSingleton('madapter/madapter')
                            ->load($madapterId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($madapterIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'madapter.csv';
        $content = $this->getLayout()->createBlock('madapter/adminhtml_madapter_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'madapter.xml';
        $content = $this->getLayout()->createBlock('madapter/adminhtml_madapter_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('madapter');
    }

    public function sendNoticeAction() {
        // Put your device token here (without spaces):
        $collectionDevice = Mage::getModel('madapter/device')->getCollection();
        $ctx = stream_context_create();
        $ch = Mage::getBaseDir('media') . DS . 'madapter' . DS . 'ck.pem';
        $passphrase = '123456';
        stream_context_set_option($ctx, 'ssl', 'local_cert', $ch);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        $fp = stream_socket_client(
                'sslv3://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);

        foreach ($collectionDevice as $item) {
            $deviceToken = $item->getDeviceToken();

            $message = Mage::helper('madapter')->getConfigNotice('message');

            $body['aps'] = array(
                'alert' => $message,
                'sound' => 'default',
                'badge' => 1
            );
            $body['url'] = Mage::helper('madapter')->getConfigNotice('url');
            $body['title'] = Mage::helper('madapter')->getConfigNotice('title');

            // Encode the payload as JSON
            $payload = json_encode($body);

            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));
            if (!$result)
                echo 'Message not delivered' . PHP_EOL;
        }
        fclose($fp);
        // $this->_forward('adminhtml/system_config/save/section/madapter');
        $this->_redirect('adminhtml/system_config/edit/section/madapter');
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Message successfully delivered'));
    }

    

}