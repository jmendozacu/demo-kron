<?php 
class Kronosav_Appointment_Block_Adminhtml_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{ 
	public function __construct()
	{
		parent::__construct();
		$this->setId('customer_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('appointment')->__('Customer Details'));
	}
 
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
			'label'     => Mage::helper('appointment')->__('General'),
			'id'     => Mage::helper('appointment')->__('General'),
			'content'   => $this->getLayout()->createBlock('appointment/adminhtml_customer_edit_tab_form')->toHtml(),
		));
		
		/**
		$this->addTab('form_address_section', array(
			'label'     => Mage::helper('appointment')->__('Address'),
			'id'     => Mage::helper('appointment')->__('Address'),
			'content'   => $this->getLayout()->createBlock('appointment/adminhtml_customer_edit_tab_address_form')->toHtml(),
		));
		*/
		
		return parent::_beforeToHtml();
	}
}