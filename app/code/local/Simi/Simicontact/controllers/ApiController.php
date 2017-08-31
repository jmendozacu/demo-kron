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
 * @package 	Magestore_Simicontact
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Simicontact Index Controller
 * 
 * @category 	Magestore
 * @package 	Magestore_Simicontact
 * @author  	Magestore Developer
 */
class Simi_Simicontact_ApiController extends Simi_Connector_Controller_Action
{
	public function get_contactsAction() {
		$information = Mage::getModel('simicontact/contact')->getContacts();
		$this->_printDataJson($information);
    }
}