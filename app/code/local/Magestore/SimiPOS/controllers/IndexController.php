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
 * SimiPOS Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_IndexController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch() {
        $this->getLayout()->setArea('adminhtml');
        Mage::app()->setCurrentStore('admin');
        $this->setFlag('', self::FLAG_NO_START_SESSION, 1); // Don't start standard session
        
        parent::preDispatch();
        return $this;
    }
    
    /**
     * SimiPOS API Action
     * 
     * Call Params:
     *  method  string  Method name will be called (login, logout, product.list...)
     *  session string  Current session ID
     *  params  string  JSON string - Parameters
     * 
     * Special: method login don't need provide session and params
     *  method      string  'login'
     *  username    string  Username
     *  password    string  Password
     * 
     * Response: JSON string
     *  success boolean true - call method successfully, false - call failed
     *  error   int     error code
     *  data    mixed   success is true: JSON string for array, otherwise for other format
     *                  success is false: error message, JSON string for array of errors
     */
    public function indexAction()
    {
        $result = array('success' => 1);
        if (!Mage::helper('simipos')->isEnable()) {
            $result['success'] = 0;
            $result['error'] = 1;
            $result['data'] = Mage::helper('simipos')->__('POS is disabled on your server. Please contact your server administrator to enable it.');
        } else if ($data = $this->getRequest()->getPost()) {
            try {
                $result['data'] = Mage::getModel('simipos/api')->run($data);
            } catch (Exception $e) {
                $result['success'] = 0;
                $result['error'] = $e->getCode();
                $result['data'] = $e->getMessage();
            }
        } else {
        	$result['success'] = 0;
        	$result['error'] = 1;
        	$result['data'] = Mage::helper('simipos')->__('Can not call API directly.');
        }
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}
