<?php 
class Kronosav_Repair_Block_Renderer_Telephone extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$customerId = $row->getId();
		$visitorData = Mage::getModel('customer/customer')->load($customerId);
		$billingaddress = Mage::getModel('customer/address')->load($visitorData->default_billing);
		$addressdata = $billingaddress ->getData();
		return $addressdata['telephone'];

		// $customer = Mage::getModel('customer/address')->load($customerId);
		// $value = $customer->getTelephone();
		// return $value;
	}
}