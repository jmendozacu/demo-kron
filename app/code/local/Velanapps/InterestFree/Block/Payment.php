<?php
class Velanapps_InterestFree_Block_Payment extends Mage_Core_Block_Template
{
	public function customerAddress()
   {
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$address = Mage::getModel('customer/address')->load($customer->getDefaultBilling());
		return $address;
   }

}
 