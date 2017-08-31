<?php 
class Kronosav_Loan_Block_Adminhtml_Loan_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();	
		$this->_objectId = 'id';
		$this->_blockGroup = 'loan';
		$this->_controller = 'adminhtml_loan'; 
		$this->_updateButton('save', 'label', Mage::helper('loan')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('loan')->__('Delete Item'));
	}
 
	public function getHeaderText()
	{
		
		 $registry=Mage::registry('customeredit_data');
		 
		if($registry) {
			return Mage::helper('loan')->__("Edit Loan");
			
		} else {
		
			return Mage::helper('loan')->__('Add Loan Details');
		}
	}
}