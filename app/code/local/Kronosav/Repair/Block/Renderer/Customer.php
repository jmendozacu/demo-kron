<?php 
class Kronosav_Repair_Block_Renderer_Customer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$customerId = $row->getId();
		$val = $this->GetDetails($customerId);
		return $val;
	}
	
	public function GetDetails($customerId) {
		echo  Mage::getModel('customer/customer')->load($customerId)->getName();
	}

}
