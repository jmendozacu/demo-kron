<?php 
class Kronosav_Loan_Block_Adminhtml_Customer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
			
			$data = array(
				'label' =>  'Back',
				'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/customer') . '\')',
				'class'     =>  'back'
		   );
		   $data1 = array(
				'label' =>  'Add Product',
				'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/select') . '\')',
				'class'     => 'add'
		   );
		parent::__construct();	
		$this->_removeButton('back'); 	
		$this->_objectId = 'id';
		$this->_blockGroup = 'loan';
		$this->_controller = 'adminhtml_customer';
		$this->addButton ('back', $data);
		$this->_removeButton ('Add Product', $data1);
		$this->_updateButton('save', 'label', Mage::helper('loan')->__('Save Item'));
		$this->_removeButton('delete', 'label', Mage::helper('loan')->__('Delete Item'));
		
	}
 
	public function getHeaderText()
	{
	    $a=Mage::registry('customeredit_data');
		if($a) {
			return Mage::helper('loan')->__("Create New loan");
			// return Mage::helper('loan')->__("Create New loan for '%s'", $this->htmlEscape(Mage::registry('customer_data')->getName()));
		} else {
			return Mage::helper('loan')->__('Add Loan Details');
		}
	}
	 

}