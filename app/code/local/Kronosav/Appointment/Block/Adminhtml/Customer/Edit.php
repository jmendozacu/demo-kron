<?php 
class Kronosav_Appointment_Block_Adminhtml_Customer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
			
			$data = array(
				'label' =>  'Back',
				'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/customer') . '\')',
				'class'     =>  'back'
		   );
		parent::__construct();	
		$this->_removeButton('back'); 	
		$this->_objectId = 'id';
		$this->_blockGroup = 'appointment';
		$this->_controller = 'adminhtml_customer';
		$this->addButton ('back', $data);
		$this->_updateButton('save', 'label', Mage::helper('appointment')->__('Save Item'));
		$this->_removeButton('delete', 'label', Mage::helper('appointment')->__('Delete Item'));
		
	}
 
	public function getHeaderText()
	{
	    $a=Mage::registry('customeredit_data');
		if($a) {
			return Mage::helper('appointment')->__("Create New Appointment");
			// return Mage::helper('appointment')->__("Create New Appointment for '%s'", $this->htmlEscape(Mage::registry('customer_data')->getName()));
		} else {
			return Mage::helper('appointment')->__('Add Appointment Details');
		}
	}
	 

}