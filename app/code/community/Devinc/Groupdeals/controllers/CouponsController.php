<?php
class Devinc_Groupdeals_CouponsController extends Mage_Core_Controller_Front_Action
{ 
    public function preDispatch()
    {
        parent::preDispatch();	
        
        if (!Mage::helper('groupdeals')->isEnabled()) {
            $this->norouteAction();
            return;
        }
    }
    
    //coupons view functions
    protected function _canViewCoupon() {
    	$coupon = Mage::getModel('groupdeals/coupons')->load($this->getRequest()->getParam('coupon_id')); 
		$item = Mage::getModel('sales/order_item')->load($coupon->getOrderItemId());
		$order = Mage::getModel('sales/order')->load($item->getOrderId());
   		$customer = Mage::getSingleton('customer/session');
   		
        if (!$coupon || $coupon->getStatus()!='complete' || $customer->getId()!=$order->getCustomerId()) {
            $this->_redirect('*/*');
            return false;
        }
        
        return true;
    }
    
	public function indexAction() {
		if(!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            return;
        }
        
        $this->loadLayout();           
        
		$this->getLayout()->getBlock('head')->setTitle($this->__('My Coupons'));
        
        $block = $this->getLayout()->getBlock('groupdeals_coupons');
        if ($block) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        
		$this->renderLayout();
	}

	public function viewAction() {
		if(!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            return;
        }
        
        if (!$this->_canViewCoupon()) {
            return;
        }
        
        $this->loadLayout();           
        
    	$coupon = Mage::getModel('groupdeals/coupons')->load($this->getRequest()->getParam('coupon_id')); 
		$this->getLayout()->getBlock('head')->setTitle($this->__('Coupon - %s', $coupon->getCouponCode()));
		
        $navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('groupdeals/coupons/index');
        }
        
		$this->renderLayout();
	}	
	
	public function printAction()
    {
        if(!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            return;
        }
        
        $this->loadLayout('print');        
        
    	$coupon = Mage::getModel('groupdeals/coupons')->load($this->getRequest()->getParam('coupon_id')); 
		$this->getLayout()->getBlock('head')->setTitle($this->__('Print Coupon - %s', $coupon->getCouponCode()));
        
        $this->renderLayout();
    }	

	//redeem coupon functions
	public function redeemAction() {
        $this->loadLayout();      
		$this->renderLayout();
	}	
	
	public function redeemCouponAction()
    {
		$post = $this->getRequest()->getPost();
        if ($post)  {
            try {
				$coupon = Mage::getModel('groupdeals/coupons')->load(trim($post['coupon_code']), 'coupon_code');
                
				if ($coupon->getId() && $coupon->getStatus()!='voided') {
					if ($coupon->getRedeem()=='not_used') {
						$coupon->setRedeem('used')->save();
						Mage::getSingleton('core/session')->addSuccess(Mage::helper('groupdeals')->__('The &quot;%s&quot; Coupon has been redeemed.', $coupon->getCouponCode()));
					} else {
						Mage::getSingleton('core/session')->addError(Mage::helper('groupdeals')->__('The &quot;%s&quot; Coupon has already been used.', $coupon->getCouponCode()));
					}
				} else {
					Mage::getSingleton('core/session')->addError(Mage::helper('groupdeals')->__('The Coupon doesn\'t exist in our database.'));
				}
				
                $this->_redirect('*/*/redeem');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('groupdeals')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/redeem');
                return;
            }

        }
        
        $this->_redirect('*/*/redeem');
    }
    
    //gift coupon to a friend functions
    public function getQuote() {
    	return Mage::getSingleton('checkout/session')->getQuote();
    }
	
	public function saveGiftAction()
    {
        $result = array();
    	$data = $this->getRequest()->getPost();
    	if (empty($data)) {
            $result = array('error' => -1, 'message' => Mage::helper('catalog')->__('Invalid data.'));
        } else {
    		$quote = $this->getQuote();
    		if ($quote->getId()) {
    		    $quote->setGroupdealsCouponFrom($data['coupon_from']);
    		    $quote->setGroupdealsCouponTo($data['coupon_to']);
    		    $quote->setGroupdealsCouponToEmail($data['coupon_to_email']);
    		    $quote->setGroupdealsCouponMessage($data['coupon_message']);
    		    $quote->save();
			} else {
        		$result = array('error' => -1, 'message' => Mage::helper('contacts')->__('Unable to submit your request. Please, try again later.'));
			}
		}        		
		
        if (!isset($result['error'])) {
            $result['update_section'] = array(
                'name' => 'gift-link',
                'html' => $this->_getGiftHtml()
            );
		}
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}		
    
    protected function _getGiftHtml()
    {
    	$_quote = $this->getQuote();
    	if ($_quote->getGroupdealsCouponTo()!='') { 
			$html = Mage::helper('groupdeals')->__('Gift for:').' <strong>'.$_quote->getGroupdealsCouponTo().'</strong><br/>
			<span id="link-actions"><a href="javascript:void(0);" onclick="giftPopup.showPopup();">'.Mage::helper('groupdeals')->__('Edit').'</a> '.Mage::helper('groupdeals')->__('or').' <a href="javascript:void(0)" onclick="gift.removeGift()">'.Mage::helper('groupdeals')->__('Remove').'</a></span>';
		} else {
			$html = '<a href="javascript:void(0)" onClick="giftPopup.showPopup();">'.Mage::helper('groupdeals')->__('Give the Coupon(s) as a Gift').'</a>';
		}
		
        return $html;
    }
}