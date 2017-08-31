<?php
class Kronosav_Loan_Block_Adminhtml_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $selectedProducts = array();
		Mage::getSingleton('adminhtml/session')->setProductDetails($selectedProducts);
		Mage::getSingleton('adminhtml/session')->setCustomerId($CustomerId);
		$this->_blockGroup = 'loan';
        $this->_controller = 'adminhtml_customer';
        $this->_headerText = $this->__('Please Select A Customer');
		$this->_addButtonLabel = $this->__('Create New Customer');
		parent::__construct();
		
	}
	
	
}
?>