<?php
class Kronosav_Appointment_Block_Adminhtml_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'appointment';
        $this->_controller = 'adminhtml_customer';
        $this->_headerText = $this->__('Please Select A Customer');
		$this->_addButtonLabel = $this->__('Create New Customer');
		parent::__construct();
		
	}
	
	
}
?>