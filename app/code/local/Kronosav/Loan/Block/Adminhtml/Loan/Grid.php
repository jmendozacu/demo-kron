<?php 
class Kronosav_Loan_Block_Adminhtml_Loan_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
	  	parent::__construct();
		$this->setId('loanGrid');
		// This is the primary key of the database
		$this->setDefaultSort('loan_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
 
	protected function _prepareCollection()
	{
	    $collection = Mage::getModel('loan/loan')->getCollection();		
		$this->setCollection($collection);
		
		return parent::_prepareCollection();
	}
 
	protected function _prepareColumns()
	{
	
		$this->addColumn('loan_id', array(
			'header'    => Mage::helper('loan')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'loan_id',
		));
		$this->addColumn('customer_name', array(
			'header'    => Mage::helper('loan')->__('Customer Name'),
			'align'     =>'left',
			'index'     =>'entity_id',
			'renderer'  => 'Kronosav_Loan_Block_Customer'
		));
		$this->addColumn('product_id', array(
			'header'    => Mage::helper('loan')->__('Product Name'),
			'align'     =>'left',
			'index'     => 'product_id',
			'renderer'  => 'Kronosav_Loan_Block_Product',
		));
		$this->addColumn('loan_description', array(
			'header'    => Mage::helper('loan')->__('Loan Description'),
			'align'     =>'left',
			'index'     => 'loan_description',
			//'renderer'  => 'Kronosav_Loan_Block_Product',
		));
		$this->addColumn('loan_from_date', array(
			'header'    => Mage::helper('loan')->__('Loan From Date'),
			'align'     =>'left',
			'index'     => 'loan_from_date',
		));
		$this->addColumn('loan_from_time', array(
			'header'    => Mage::helper('loan')->__('Loan To Date'),
			'align'     =>'left',
			'index'     =>'loan_to_date',
		));
		
		$this->addColumn('status', array(
			'header'    => Mage::helper('loan')->__('Status'),
			'align'     =>'left',
			'index'     =>'status',
			'type'		=>'options',
			'options'   => array('1'=>'New',
			  '2'=>'Completed',
			  '3'=>'Cancelled'),
		));
				
		return parent::_prepareColumns();
	}
	protected function _prepareMassaction()
    {
        $this->setMassactionIdField('loan_id');
        $this->getMassactionBlock()->setFormFieldName('id');
 
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('loan')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('loan')->__('Are you sure?')
        ));
		$this->getMassactionBlock()->addItem('pdf', array(
             'label'    => Mage::helper('loan')->__('Export to PDF'),
             'url'      => $this->getUrl('*/*/createPdf'),
        ));
       return $this;  
	}
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
 
	public function getGridUrl()
	{
	  return $this->getUrl('*/*/grid', array('_current'=>true));
	}
 
 
}