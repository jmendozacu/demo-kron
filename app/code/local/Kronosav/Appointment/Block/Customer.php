<?php
 
class Kronosav_Appointment_Block_Customer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{  
 
public function render(Varien_Object $row)
{
$customerId =  $row->getData($this->getColumn()->getIndex());
$customer = Mage::getModel('customer/customer')->load($customerId);
 
$value = $customer->getName();
 
return $value;
}
}