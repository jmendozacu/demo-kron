<?php
require_once Mage::getModuleDir('controllers', 'Mage_Catalog').DS.'ProductController.php';

class Devinc_Groupdeals_ProductController extends Mage_Catalog_ProductController
{			
	//initialize groupdeal
	public function preDispatch()
    {
        parent::preDispatch();
        
        $groupdealId = $this->getRequest()->getParam('groupdeals_id', false);
        if (!Mage::helper('groupdeals')->isEnabled() && $groupdealId) {
            $this->norouteAction();
            return;
        }
    }
        
	public function viewAction() 
	{
        $groupdealId = $this->getRequest()->getParam('groupdeals_id', false);
		$groupdeal = Mage::getModel('groupdeals/groupdeals')->load($groupdealId);
		if ($groupdeal->getId()) {	
			Mage::register('groupdeals', $groupdeal);
		} else {			
            $this->norouteAction();
            return;
		}
		
		parent::viewAction();
	}
		
	//function DEPRECATED
	//homepage redirect
	public function redirectAction()
    {      
    	/*
$storeId = Mage::app()->getStore()->getId();
		$city = Mage::getStoreConfig('groupdeals/configuration/homepage_deals');
		if ($city!='default') {
			$pastDeals = false;
			$queuedDeals = false;
			$groupdealId = '';
			$groupdealCollection = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('city', $city)->setOrder('position', 'ASC')->setOrder('groupdeals_id', 'DESC');
			if (count($groupdealCollection)>0) {
				foreach ($groupdealCollection as $groupdeal) {	
					$product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($groupdeal->getProductId());
					if ($product->getGroupdealStatus()==Devinc_Groupdeals_Model_Source_Status::STATUS_RUNNING) {		
						$mainProduct = $product;
						$groupdealId = $groupdeal->getId();
						break;
					} elseif ($product->getGroupdealStatus()==Devinc_Groupdeals_Model_Source_Status::STATUS_ENDED) {
						$pastDeals = true;
					} elseif ($product->getGroupdealStatus()==Devinc_Groupdeals_Model_Source_Status::STATUS_QUEUED) {
						$queuedDeals = true;
					}
				}
			}
				
			if ($groupdealId!='') {					
				if (Mage::getStoreConfig('groupdeals/configuration/deals_view')==0) {
					if ($mainProduct->getUrlPath()!='') {
						$this->_redirectUrl(substr(Mage::getUrl($mainProduct->getUrlPath()),0,-1));
					} else {			
						$this->_redirect('groupdeals/product/view', array('id'=>$mainProduct->getId(), 'groupdeals_id'=>$groupdealId));
					}
				} else {
					$this->_redirect('groupdeals/product/list', array('city'=>rawurlencode($city)));
				}
			} elseif ($pastDeals) {
				$this->_redirect('groupdeals/product/recent', array('city'=>rawurlencode($city)));
			} elseif ($queuedDeals) {
				$this->_redirect('groupdeals/product/upcoming', array('city'=>rawurlencode($city)));
			} else {
				$this->_redirect(Mage::getStoreConfig('web/default/cms_home_page'));
			}			
			return;
		} else {
			$this->_redirect(Mage::getStoreConfig('web/default/cms_home_page'));
			return;
		}
*/
		$this->_redirect(Mage::getStoreConfig('web/default/cms_home_page'));
	}
	
	//refresh deals
	public function refreshAction()
    {      
    	Mage::getModel('groupdeals/groupdeals')->refreshGroupdeals();
        Mage::getSingleton('core/session')->addSuccess($this->__('All Deals have been refreshed'));
    	$this->_redirect(Mage::getStoreConfig('web/default/cms_home_page'));
		return;
	}
	
	public function listAction()
    {      
		$this->loadLayout();
		//set normal/detailed list				
		if (Mage::getStoreConfig('groupdeals/configuration/list_type')==1) {
			$template = 'groupdeals/product/list_detailed.phtml';
		} else {
			$template = 'groupdeals/product/list.phtml';
		}
		$block = $this->getLayout()->createBlock('groupdeals/product_list', 'groupdeals_product_list', array('template' => $template));		
		$this->getLayout()->getBlock('content')->append($block);
		
		if (Mage::helper('groupdeals')->getMagentoVersion()>1420 && Mage::helper('groupdeals')->getMagentoVersion()<1800) {
			$this->initLayoutMessages(array('catalog/session', 'tag/session', 'checkout/session'));
		}
		
		$this->renderLayout();      
    }
	
	public function recentAction()
    {      
		$this->loadLayout();		
		$this->renderLayout();      
    }
	
	public function upcomingAction()
    {     
    	if (!Mage::getStoreConfig('groupdeals/configuration/display_upcoming')) {
            $this->norouteAction();
            return;
        } 
        
		$this->loadLayout();
		$this->renderLayout();      
    }
	
    public function noDealsAction()
    {
        Mage::getSingleton('core/session')->addError($this->__('There are no deals setup at the moment'));
		
        $this->_redirect('');
        return;
    }
    
}