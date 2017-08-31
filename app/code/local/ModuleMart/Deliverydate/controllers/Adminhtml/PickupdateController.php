<?php

class ModuleMart_Deliverydate_Adminhtml_PickupdateController extends Mage_Adminhtml_Controller_action
{
	
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('deliverydate/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}
	
	protected function sendAction() {
		 $deliverydate = explode('/',$this->getRequest()->getParam('pickupdate'));
		
		$timestamp = strtotime($this->getRequest()->getParam('pickupdate'));

		 $day = date('l', $timestamp);
		 $month = date('F', mktime(0, 0, 0, $deliverydate[0], 10));		
		// admin session
		$session = Mage::getSingleton('adminhtml/session');
		if($this->getRequest()->getParam('pickupdate')== '') {
			$session->addError($this->__('Please enter Pickup Date for order #'.$this->getRequest()->getParam('real_order_id')));
		} else {
			$collection = Mage::getModel('deliverydate/deliverydate')->getCollection();
			$collection->addFieldToFilter('order_id',array('eq'=>$this->getRequest()->getParam('order_id')));
			// check if order id is available for delivery date.
			$pickupData = $collection->getData();
			
			$order = Mage::getModel('sales/order')->loadByIncrementId($this->getRequest()->getParam('real_order_id'));
			
			if($order->getShippingDescription() == "Store Pick Up - Store Pick Up (Staplefield, West Sussex)" || $order->getShippingDescription() == "Store Pick Up - Store Pick Up (Dungannon, N.Ireland)"){
			
				if($collection->getSize() >= 1 && $pickupData[0]["is_pickup_date"] == "1") {
					 $session->addError($this->__('Pickup Date has already been sent for order #'.$this->getRequest()->getParam('real_order_id')));
				} else {
					

					if($order->getShippingDescription() == "Store Pick Up - Store Pick Up (Dungannon, N.Ireland)"){
						
$pickupAddress = <<<EOH

<div style="margin:auto;">
	<div style="font-size: 14px;margin-bottom: 10px;font-weight: bold;color: rgb(241, 10, 10);">Address:<br /></div>
	Kronos N.Ireland<br />
	8-9 Scotch Street Center, Dungannon<br />
	BT701AR Co.Tyrone<br />
</div>

EOH;
					}else{

$pickupAddress = <<<EOH

<div style="margin:auto;">
	<div style="font-size: 14px;margin-bottom: 10px;font-weight: bold;color: rgb(241, 10, 10);">Address:<br /></div>
	Kronos Brighton<br />
	2-7 Horsted Square, Staplefield,<br /><br />
</div>

EOH;

					}
					
					
					
					// get delivery date model
					$model = Mage::getModel('deliverydate/deliverydate');
					// set values in delivery date table
					$model->setOrderId($this->getRequest()->getParam('order_id'));
					$model->setRealOrderId($this->getRequest()->getParam('real_order_id'));
					$model->setDeliveryDate($this->getRequest()->getParam('pickupdate'));
					$model->setCreatedAt(date('Y-m-d'));
					$model->setIsPickupDate(1);
					// save model data in db
					$model->save();
					//send email to user
					$email = $order->getCustomerEmail(); //'gary@kronosav.com'; //
					
					$mailSubject = 'Pickup Date for Order#'.$this->getRequest()->getParam('real_order_id');
					$sender = array('name' => 'Kronos Audio Visual', 'email' => $email);
					$vars = array();
					$vars = array('customer_name'=>$order->getCustomerName(), 'order'=>$this->getRequest()->getParam('real_order_id'), 'day'=>$day, 'd_month'=>$month, 'd_date'=>$deliverydate[1], 'd_year'=>$deliverydate[2], 'pickup_address' => $pickupAddress);
					
					$_mail_for_user = Mage::getModel('core/email_template');
					
					$_mail_for_user->setDesignConfig(array('area' => 'frontend', 'store' => Mage::app()->getStore()->getId()))
					->setTemplateSubject($mailSubject)
					 ->sendTransactional(
							Mage::getStoreConfig('deliverydate/email/pickup_template'),
							$sender, $email, null, $vars, Mage::app()->getStore()->getId());
					$_mail_for_user->setTranslateInline(true);	
					// success message
					$session->addSuccess($this->__('Pickup Date has been sent for order #'.$this->getRequest()->getParam('real_order_id')));
				}
			
			} else {
				$session->addError($this->__('The customer has not chosen Store Pickup for order #'.$this->getRequest()->getParam('real_order_id') . '. You cannot send Pickup Date for this order.'));
			}
		}
		$this->_redirect('adminhtml/sales_order/view', array('order_id' => $this->getRequest()->getParam('order_id')));	
	}
	
}