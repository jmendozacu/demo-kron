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
 * Simipos User Controller
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Adminhtml_MagestoreController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * @return Magestore_SimiPOS_Helper_Magestore
	 */
	protected function _helper()
	{
		return Mage::helper('simipos/magestore');
	}
	
	protected function saveConfig($field, $value)
	{
		Mage::getConfig()->saveConfig('simipos/account/' . $field, $value);
	}
	
	public function loginAction()
	{
		if ($this->getRequest()->isPost()) {
		  // Login to Magestore
		  try {
		  	$username = $this->getRequest()->getPost('username');
		  	$password = $this->getRequest()->getPost('password');
		    $apiKey = $this->_helper()->login($username, $password);
		    
		    // Save username and password to config
		    $this->saveConfig('username', $username);
		    $this->saveConfig('mode', $this->getRequest()->getPost('mode'));
		    $this->saveConfig('api_key', $apiKey);
		    Mage::getConfig()->cleanCache();
		    
		    $this->_keepSectionOpen();
		    Mage::getSingleton('adminhtml/session')->addSuccess($this->_helper()->__('Your store has been connected to SimiPOS server'));
		  } catch (Exception $e) {
		  	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		  }
		}
		$this->_redirect('adminhtml/system_config/edit', array('section' => 'simipos'));
	}
	
	public function storeAction()
	{
		if ($this->getRequest()->isPost()) {
		    try {
		    	$packageId = $this->getRequest()->getPost('term_id');
		    	$this->_helper()->changePackage($packageId);
		    	
		    	$this->_keepSectionOpen();
		        Mage::getSingleton('adminhtml/session')->addSuccess($this->_helper()->__('Your store has been connected to SimiPOS server'));
		    } catch (Exception $e) {
		    	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		    	$this->saveConfig('api_key', '');
		    	Mage::getConfig()->cleanCache();
		    }
		}
		$this->_redirect('adminhtml/system_config/edit', array('section' => 'simipos'));
	}
	
	public function logoutAction()
	{
		try {
			$this->saveConfig('api_key', '');
			$this->saveConfig('term_id', '');
			$this->saveConfig('term_options', '');
			Mage::getConfig()->cleanCache();
			
			$this->_keepSectionOpen();
			Mage::getSingleton('adminhtml/session')->addSuccess($this->_helper()->__('Your store has been disconnected from SimiPOS server'));
		} catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('adminhtml/system_config/edit', array('section' => 'simipos'));
	}
	
    protected function _keepSectionOpen()
    {
        $adminUser = Mage::getSingleton('admin/session')->getUser();
        $extra = $adminUser->getExtra();

        if (!is_array($extra)) {
            $extra = array();
        }
        if (!isset($extra['configState'])) {
            $extra['configState'] = array();
        }

        $extra['configState']['simipos_account'] = 1;
        $adminUser->saveExtra($extra);
        return $this;
    }
}
