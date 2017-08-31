<?php
class Devinc_Groupdeals_SubscriberController extends Mage_Core_Controller_Front_Action
{ 
    public function preDispatch()
    {
        parent::preDispatch();
        
        if (!Mage::helper('groupdeals')->isEnabled()) {
            $this->norouteAction();
            return;
        }
    }
    
	public function unsubscribeSuccessAction() {
		$this->loadLayout();
		$this->renderLayout();
	}	
	
	public function subscribeAction()
    {
		$post = $this->getRequest()->getPost();
        if ($post)  {
            try {
				if (!isset($post['city'])) {
            		$city = Mage::helper('groupdeals')->getCity();
					if (isset($city) && $city!='') {
						$post['city'] = $city;
					} else {
						Mage::getSingleton('core/session')->addError($this->__('Session expired. Please select a city.'));	
        				$this->_redirectUrl($this->_getRefererUrl());
						return;
					}
				}
				
				$post['store_id'] = Mage::app()->getStore()->getId();
            	          	
                $error = false;
                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
                
                $subscriber = Mage::getModel('groupdeals/subscribers')->getCollection()->addFieldToFilter('email', $post['email'])->addFieldToFilter('store_id', $post['store_id'])->addFieldToFilter('city', $post['city'])->getFirstItem();				
				if (!$subscriber->getId()) {		
					Mage::getModel('groupdeals/subscribers')
						->setData($post)			
						->save();		
					Mage::getSingleton('core/session')->addSuccess($this->__('Thank you for your subscription.'));	
				} else {
					Mage::getSingleton('core/session')->addError($this->__('You are already subscribed to this city.'));	
				}				             
            } catch (Exception $e) {
				Mage::getSingleton('core/session')->addError(Mage::helper('groupdeals')->__('Unable to submit your request. Please, try again later'));
			}
		} 
        
        $this->_redirectUrl($this->_getRefererUrl());
    }
	
	public function unsubscribeAction() {
		$this->loadLayout()->renderLayout();
		
		$subscriberId = trim(base64_decode($this->getRequest()->getParam('subscriber_id')));
		$subscriber = Mage::getModel('groupdeals/subscribers')->load($subscriberId);
		$city = $subscriber->getCity();
		
		if ($subscriber->getId()) {					 
			$subscriber->delete();
		}
					 
		$this->_redirect('groupdeals/subscriber/unsubscribeSuccess', array('city' => rawurlencode($city)));
	}	
	
}