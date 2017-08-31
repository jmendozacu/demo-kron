<?php 
class Kronosav_Appointment_Block_Adminhtml_Appointment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();	
		$this->_removeButton('reset');
		$this->_objectId = 'id';
		$this->_blockGroup = 'appointment';
		$this->_controller = 'adminhtml_appointment'; 
		$this->_updateButton('save', 'label', Mage::helper('appointment')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('appointment')->__('Delete Item'));
	}
 
	public function getHeaderText()
	{
		
		 $registry=Mage::registry('customeredit_data');
		 
		if($registry) {
			return Mage::helper('appointment')->__("Edit Appointment");
			
		} else {
			return Mage::helper('appointment')->__('Add Appointment Details');
		}
	}
}