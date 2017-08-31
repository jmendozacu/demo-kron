<?php 
class Kronosav_Loan_Block_Adminhtml_Loan_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{ 
	public function __construct()
	{
		parent::__construct();
		$this->setId('loan_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('loan')->__('loan Details'));
	}
 
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
			'label'     => Mage::helper('loan')->__('General'),
			'id'     => Mage::helper('loan')->__('General'),
			'content'   => $this->getLayout()->createBlock('loan/adminhtml_loan_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}