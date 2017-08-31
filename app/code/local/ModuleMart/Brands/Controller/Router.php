<?php 
 /**
 * ModuleMart_Brands extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Module-Mart License
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.modulemart.com/license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to modules@modulemart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.modulemart.com for more information.
 *
 * @category   ModuleMart
 * @package    ModuleMart_Brands
 * @author-email  modules@modulemart.com
 * @copyright  Copyright 2014 © modulemart.com. All Rights Reserved
 */
class ModuleMart_Brands_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract{
	/**
	 * init routes
	 * @access public
	 * @param Varien_Event_Observer $observer
	 */
	public function initControllerRouters($observer){
		$front = $observer->getEvent()->getFront();
		$front->addRouter('brands', $this);
		return $this;
	}
	/**
	 * Validate and match entities and modify request
	 * @access public
	 * @param Zend_Controller_Request_Http $request
	 */
	public function match(Zend_Controller_Request_Http $request){
		if (!Mage::isInstalled()) {
			Mage::app()->getFrontController()->getResponse()
				->setRedirect(Mage::getUrl('install'))
				->sendResponse();
			exit;
		}
		$urlKey = trim($request->getPathInfo(), '/');
		$check = array();
		$check['brand'] = new Varien_Object(array(
			'model' =>'brands/brand',
			'controller' => 'brand',
			'action' => 'view',
			'param'	=> 'id',
		));
		foreach ($check as $key=>$settings){
			$model = Mage::getModel($settings->getModel());
			$id = $model->checkUrlKey($urlKey, Mage::app()->getStore()->getId());
			if ($id){
				$request->setModuleName('brands')
					->setControllerName($settings->getController())
					->setActionName($settings->getAction())
					->setParam($settings->getParam(), $id);
				$request->setAlias(
					Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
					$urlKey
				);
				return true;
			}
		}
		return false;
	}
}