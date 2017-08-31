<?php
class Kronosav_Appointment_Block_Adminhtml_Appointment extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'appointment';
        $this->_controller = 'adminhtml_appointment';
        $this->_headerText = $this->__('Kronosav Appointment');
		$this->_addButtonLabel = $this->__('Create New Appointment');
        parent::__construct();
		
    }
	public function getCreateUrl()
	{
		return $this->getUrl('*/*/customer');
	}
}
?>