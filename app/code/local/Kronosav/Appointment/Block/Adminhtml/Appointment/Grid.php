<?php 
class Kronosav_Appointment_Block_Adminhtml_Appointment_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
	  	parent::__construct();
		$this->setId('appointmentGrid');
		// This is the primary key of the database
		$this->setDefaultSort('appointment_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
 
	protected function _prepareCollection()
	{
	    $collection = Mage::getModel('appointment/appointment')->getCollection();		
		$this->setCollection($collection);
		
		return parent::_prepareCollection();
	}
 
	protected function _prepareColumns()
	{
	
		$this->addColumn('appointment_id', array(
			'header'    => Mage::helper('appointment')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'appointment_id',
		));
		$this->addColumn('customer_name', array(
			'header'    => Mage::helper('appointment')->__('Customer Name'),
			'align'     =>'left',
			'index'     =>'entity_id',
			'renderer'  => 'Kronosav_Appointment_Block_Customer'
		));
		$this->addColumn('appointment_description', array(
			'header'    => Mage::helper('appointment')->__('Appointment Description'),
			'align'     =>'left',
			'index'     => 'appointment_description',
		));
		$this->addColumn('appointment_date', array(
			'header'    => Mage::helper('appointment')->__('Appointment Date'),
			'align'     =>'left',
			'index'     => 'appointment_date',
		));
		$this->addColumn('appointment_time', array(
			'header'    => Mage::helper('appointment')->__('Appointment Time'),
			'align'     =>'left',
			'index'     =>'appointment_time',
			
		));
		
		$this->addColumn('created_at', array(
			'header'    => Mage::helper('appointment')->__('Created At'),
			'align'     =>'left',
			'index'     =>'created_at',
		));
		$this->addColumn('location', array(
			'header'    => Mage::helper('appointment')->__('Location'),
			'align'     =>'left',
			'index'     =>'location',
			'type'		=>'options',
			'options'   => array('In Store'=>'In Store',
			  'Customer Premises'=>'Customer Premises'),
		));
		$this->addColumn('status', array(
			'header'    => Mage::helper('appointment')->__('Status'),
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
        $this->setMassactionIdField('appointment_id');
        $this->getMassactionBlock()->setFormFieldName('id');
 
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('appointment')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('appointment')->__('Are you sure?')
        ));
		$this->getMassactionBlock()->addItem('pdf', array(
             'label'    => Mage::helper('appointment')->__('Export to PDF'),
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