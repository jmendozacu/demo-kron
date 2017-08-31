<?php
class Devinc_Groupdeals_MerchantController extends Mage_Core_Controller_Front_Action
{	
	public function preDispatch()
    {
        parent::preDispatch();
        
        if (!Mage::helper('groupdeals')->isEnabled() || !Mage::getStoreConfig('groupdeals/merchants_subscribe/enabled')) {
            $this->norouteAction();
            return;
        }
    }
    
	public function subscribeAction()
    {  
		$this->loadLayout();
        $this->_initLayoutMessages('core/session');
		$this->renderLayout(); 
	}
    
    public function postAction()
    {
		$post = $this->getRequest()->getPost();
        if ($post)  {
            try {
            	$post['status'] = 3;  
            	
            	//encode addresses
            	$address = '';
				for ($i = 1; $i<=5; $i++) {
					if ($post['address_'.$i]!='') {
						$address .= $post['address_'.$i].'_;_';
					}
				}
				$post['address'] = substr($address,0,-3);    

                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['description']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }
				               
                Mage::getModel('groupdeals/merchants')->setData($post)->save();
                
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('groupdeals')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/subscribe');

                return;               
            } catch (Exception $e) {
				Mage::getSingleton('core/session')->addError(Mage::helper('groupdeals')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/subscribe');
                return;
            }

        } 
        
        $this->_redirect('*/*/subscribe');
    }
}