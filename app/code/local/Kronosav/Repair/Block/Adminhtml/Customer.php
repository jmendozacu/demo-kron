<?php
class Kronosav_Repair_Block_Adminhtml_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_blockGroup = 'repair';
		$this->_controller = 'adminhtml_customer';
		$this->_headerText = Mage::helper('repair')->__('Please Select A Customer');
		$this->_addButtonLabel = Mage::helper('repair')->__('Create New Customer');
		parent::__construct();
	}
		public function getCreateUrl()
    {
        return $this->getUrl('*/*/newcustomer');
    }
}