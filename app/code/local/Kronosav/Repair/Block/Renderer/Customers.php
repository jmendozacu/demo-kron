<?php 
class Kronosav_Repair_Block_Renderer_Customers extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$customerId = $row->getData($this->getColumn()->getIndex());
		$customer = Mage::getModel('customer/customer')->load($customerId);
		$value = $customer->getName();
		return $value;
	}
}