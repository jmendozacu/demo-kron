<?php
class Kronosav_Loan_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
	  	parent::__construct();
		$this->setId('loancustomerGrid');
		// This is the primary key of the database
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
 
	protected function _prepareCollection()
	{
	   $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect('email');
					
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
 
	 protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>Mage::helper('loan')->__('ID'),
            'width'     =>'50px',
            'index'     =>'entity_id',
            'align'     => 'right',
        ));
        $this->addColumn('name', array(
            'header'    =>Mage::helper('loan')->__('Name'),
            'index'     =>'name'
        ));
        $this->addColumn('email', array(
            'header'    =>Mage::helper('loan')->__('Email'),
            'width'     =>'150px',
            'index'     =>'email'
		));
		
		return parent::_prepareColumns();
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/select', array('id' => $row->getId()));
	}
 
	public function getGridUrl()
	{
	  return $this->getUrl('*/*/customergrid', array('_current'=>true));
	}
 
 
}