<?php
class Velanapps_Winners_IndexController extends Mage_Core_Controller_Front_Action{
	
	 public function preDispatch()
	{
		parent::preDispatch();
						 
		if (!Mage::getSingleton('customer/session')->authenticate($this)) {
			 $this->setFlag('', 'no-dispatch', true);
			Mage::getSingleton('customer/session')->addError('Please Login to View the Winners page');
		} 
	}	 
	public function indexAction(){
		
		$this->loadLayout();
		$this->renderLayout();
	/* 	Zend_Debug::dump($this->getLayout()->getUpdate()->getHandles()); exit; */

		
	}
	 public function discountAction()
	{
		$data = $this->getRequest()->getParams('data');
		$code = $data['coupon_code'];
		$array = array('v74cmhtj','sjga59if','4s35e4tg','22p7msk3','tophmj4i');
		$key = array_search($code, $array);
		if($key){			
			$this->_redirect('*/*/success');
		}
		else{
			$this->_redirect('*/*/dran');
		}
	
	} 
	public function successAction(){
			$this->loadLayout();
			$this->renderLayout();
	}
	public function dranAction(){
		$this->loadLayout();
		$this->renderLayout();
	} 

}
?>