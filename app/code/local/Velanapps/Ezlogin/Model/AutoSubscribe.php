<?php 
class Velanapps_Ezlogin_Model_AutoSubscribe {
	
	public function salesOrderPlaceAfter($observer)
    {        
    	$email = $observer->getEvent()->getOrder()->getCustomerEmail();
 
        //Mage::log('salesOrderPlaceAfter: '.$email, null, 'autoSubscribeEmails.log', true);
 
        $this->_autoSubscribe($email);
    }
	
	public function customerRegisterSuccess($observer) {
		
		$email = $observer->getEvent()->getCustomer()->getEmail();
        
		//Mage::log('customerRegisterSuccess: '.$email, null, 'autoSubscribeEmails.log', true);
 
        $this->_autoSubscribe($email);
	}
	
	protected function _autoSubscribe($email)
    {
        //Mage::log('_autoSubscribe: '.$email, null, 'autoSubscribeEmails.log', true);
 
        $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
        if($subscriber->getStatus() != Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED &&
                $subscriber->getStatus() != Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED) {
            $subscriber->setImportMode(true)->subscribe($email);
        }
    }    
}
?>