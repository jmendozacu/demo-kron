<?php
class Kronosav_Loan_Block_Adminhtml_Loan extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'loan';
        $this->_controller = 'adminhtml_loan';
        $this->_headerText = $this->__('Kronosav Loan');
		$this->_addButtonLabel = $this->__('Create New Loan');
        parent::__construct();
		
		
    }
	public function getCreateUrl()
	{
		return $this->getUrl('*/*/customer');
	}
}
?>