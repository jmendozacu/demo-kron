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
class Magestore_Madapter_Adminhtml_NoticeController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Madapter_Adminhtml_MadapterController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('madapter/notice')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Notification'), Mage::helper('adminhtml')->__('Notification'));
        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    public function saveAction() {
        $config = Mage::getConfig();
        $data = $this->getRequest()->getPost();
        //Zend_debug::dump($data);die();
        $config->saveConfig('madapter/notice/message', $data['message']);
        $config->saveConfig('madapter/notice/title', $data['title']);
        $config->saveConfig('madapter/notice/url', $data['url']);
        $this->sendNoticeAction();
        $this->_redirect('*/*/');
    }

    public function sendNoticeAction() {
        // Put your device token here (without spaces):
        $collectionDevice = Mage::getModel('madapter/device')->getCollection();
        $ctx = stream_context_create();
        $ch = Mage::getBaseDir('media') . DS . 'madapter' . DS . 'push.pem';
        $dir = Mage::getBaseDir('media') . DS . 'madapter' . DS . 'pass_pem.config';
        $passphrase = file_get_contents($dir);
//        $passphrase = '123456';
        stream_context_set_option($ctx, 'ssl', 'local_cert', $ch);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        if (Mage::helper('madapter')->getConfig('is_sanbox') == '0') {
            $fp = stream_socket_client(
                    'sslv3://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        } else {
            $fp = stream_socket_client(
                    'sslv3://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        }
        if (!$fp) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('madapter')->__("Failed to connect:" . $err . $errstr . PHP_EOL));
            return;
        }

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
            if (!$result) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('madapter')->__('Message not delivered' . PHP_EOL));
                return;
            }
        }
        fclose($fp);
        // $this->_forward('adminhtml/system_config/save/section/madapter');        
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Message successfully delivered'));
    }

}