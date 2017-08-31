<?php

class ModuleMart_Deliverydate_Adminhtml_DeliverydateController extends Mage_Adminhtml_Controller_action
{
	
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('deliverydate/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
	
	protected function sendAction() {
		 $deliverydate = explode('/',$this->getRequest()->getParam('deliverydate'));
		
		$timestamp = strtotime($this->getRequest()->getParam('deliverydate'));

		 $day = date('l', $timestamp);
		 $month = date('F', mktime(0, 0, 0, $deliverydate[0], 10));		
		// admin session
		$session = Mage::getSingleton('adminhtml/session');
		if($this->getRequest()->getParam('deliverydate')== '') {
			$session->addError($this->__('Please enter Delivery Date for order #'.$this->getRequest()->getParam('real_order_id')));
		} else {
			$collection = Mage::getModel('deliverydate/deliverydate')->getCollection();
			$collection->addFieldToFilter('order_id',array('eq'=>$this->getRequest()->getParam('order_id')));
			
			$order = Mage::getModel('sales/order')->loadByIncrementId($this->getRequest()->getParam('real_order_id'));
			
			if($order->getShippingDescription() != "Store Pick Up - Store Pick Up (Staplefield, West Sussex)" && $order->getShippingDescription() != "Store Pick Up - Store Pick Up (Dungannon, N.Ireland)"){
				
				// check if order id is available for delivery date.
				if($collection->getSize() >= 1) {
					 $session->addError($this->__('Delivery/Pickup Date has already been sent for order #'.$this->getRequest()->getParam('real_order_id')));
				} else {
				
					// get delivery date model
					$model = Mage::getModel('deliverydate/deliverydate');
					// set values in delivery date table
					$model->setOrderId($this->getRequest()->getParam('order_id'));
					$model->setRealOrderId($this->getRequest()->getParam('real_order_id'));
					$model->setDeliveryDate($this->getRequest()->getParam('deliverydate'));
					$model->setCreatedAt(date('Y-m-d'));
					// save model data in db
					$model->save();
					$order = Mage::getModel('sales/order')->loadByIncrementId($this->getRequest()->getParam('real_order_id'));
					//send email to user
					$email = $order->getCustomerEmail(); //'gary@kronosav.com'; //
					$mailSubject = 'Estimated Delivery Date for Order#'.$this->getRequest()->getParam('real_order_id');
					$sender = array('name' => 'Admin', 'email' => $email);
					$vars = array();
					$vars = array('customer_name'=>$order->getCustomerName(), 'order'=>$this->getRequest()->getParam('real_order_id'), 'day'=>$day, 'd_month'=>$month, 'd_date'=>$deliverydate[1], 'd_year'=>$deliverydate[2]);
					
					$_mail_for_user = Mage::getModel('core/email_template');
					
					$_mail_for_user->setDesignConfig(array('area' => 'frontend', 'store' => Mage::app()->getStore()->getId()))
					->setTemplateSubject($mailSubject)
					 ->sendTransactional(
							Mage::getStoreConfig('deliverydate/email/delivery_template'),
							$sender, $email, null, $vars, Mage::app()->getStore()->getId());
					$_mail_for_user->setTranslateInline(true);				
					// success message		
					$session->addSuccess($this->__('Delivery Date has been sent for order #'.$this->getRequest()->getParam('real_order_id')));
				}
			
			}else {
				
				$session->addError($this->__('The customer has chosen Store Pickup for order #'.$this->getRequest()->getParam('real_order_id') . '. You cannot send Delivery Date for this order.'));
				
			}
		}
		$this->_redirect('adminhtml/sales_order/view', array('order_id' => $this->getRequest()->getParam('order_id')));	
	}
	
}