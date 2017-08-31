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
 * @package     Magestore_Hideaddress
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Hideaddress Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_Hideaddress
 * @author      Magestore Developer
 */
class Simi_Hideaddress_ApiController extends Simi_Connector_Controller_Action
{
    /**
     * index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
     public function get_address_showAction(){
         // Mage::helper('debug')->save("thanh tung");
        $data=$this->getData(); 
        $information=Mage::getModel('hideaddress/hideaddress_show')->getAddressShow($data);
        $this->_printDataJson($information);
    }
     public function get_term_showAction(){
        $data=$this->getData(); 
        $information=Mage::getModel('hideaddress/hideaddress_show')->getTerm($data);
        $this->_printDataJson($information);
    }
}