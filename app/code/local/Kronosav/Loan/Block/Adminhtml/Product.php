<?php
class Kronosav_Loan_Block_Adminhtml_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
		if($this->getRequest()->getParam('id'))
		{
			$CustomerId = $this->getRequest()->getParam('id');
		}
		else
		{
			$CustomerId = Mage::getSingleton('adminhtml/session')->getCustomerId(); 
			
		}
		
	  	$this->_blockGroup = 'loan';
        $this->_controller = 'adminhtml_product';
        $this->_headerText = $this->__('Please Select A Product');
		$this->_removeButtonLabel = $this->__('Add Product');
		parent::__construct();
		$this->removeButton('add');
		$data = array(
				'label' =>  'Back',
				'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/customer') . '\')',
				'class'     =>  'back'
		   );
		$data1 = array(
				'label' =>  'Continue',
				'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/create' ,array('id' =>$CustomerId)).'\')',
				'class'     =>  'continue'
		   );
	   $this->addButton ('back', $data);
	   $this->addButton ('continue', $data1);
	
	}
	
	
}
?>