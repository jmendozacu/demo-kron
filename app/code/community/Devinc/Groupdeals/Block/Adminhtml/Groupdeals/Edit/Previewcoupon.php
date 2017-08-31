<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Previewcoupon extends Mage_Adminhtml_Block_Widget_Form
{  
  public function __construct()
  {
      parent::__construct();
      $this->setTemplate('groupdeals/preview.phtml');
  }
  
  public function getCouponHtml()
  {
    $groupdeal = Mage::getModel('groupdeals/groupdeals')->load($this->getRequest()->getParam('groupdeals_id'));
	$storeId = $this->getRequest()->getParam('store', 0);
	if ($couponId = $this->getRequest()->getParam('coupon_id', false)) {
		$coupon = Mage::getModel('groupdeals/coupons')->load($couponId);
		$orderItem = Mage::getModel('sales/order_item')->load($coupon->getOrderItemId());
		$order = Mage::getModel('sales/order')->load($orderItem->getOrderId());
		$customerName = $order->getBillingAddress()->getName();
		$storeId = $order->getStoreId();
		$html = Mage::getModel('groupdeals/coupons')->getCouponHtml($groupdeal, $storeId, $coupon, $orderItem, $customerName);
	} else {
		$html = Mage::getModel('groupdeals/coupons')->getCouponHtml($groupdeal, $storeId, null, null, 'JOHN DOE');
	}
	
	return $html;
  }
}