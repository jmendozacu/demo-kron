<?php
class Kronosav_Repair_Block_Adminhtml_Repair_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('repairGrid');
		$this->setUseAjax(true);
		$this->setDefaultSort('repair_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('repair/repair')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	protected function _prepareColumns()
	{
		$this->addColumn('repair_id',array(
			'header' => Mage::helper('repair')->__('ID'),
			'width'  => '20px',
			'index'  => 'repair_id',
			'type'   =>'number',
			));
		$this->addColumn('first_name',array(
			'header' => Mage::helper('repair')->__('Customer name'),
			'width'  => '150px',
			'index'  => 'entity_id',
			'sortable'  => false,
			'filter'    => false,
			'renderer'=> 'Kronosav_Repair_Block_Renderer_Customers'
			)); 
		$this->addColumn('product_make',array(
			'header' => Mage::helper('repair')->__('Make'),
			'width'  => '150px',
			'index'  => 'product_make',
			));
		$this->addColumn('product_model',array(
			'header' => Mage::helper('repair')->__('Model'),
			'width'  => '150px',
			'index'  => 'product_model',
			));
		$this->addColumn('serial_no',array(
			'header' => Mage::helper('repair')->__('Serial no'),
			'width'  => '150px',
			'index'  => 'serial_no',
			));
		$this->addColumn('received_date',array(
			'header' => Mage::helper('repair')->__('Received Date'),
			'width'  => '200px',
			'index'  => 'received_date',
			'type'   =>'date',
			));
		$this->addColumn('updated_date',array(
			'header' => Mage::helper('repair')->__('Updated Date'),
			'width'  => '200px',
			'index'  => 'updated_date',
			'type'   =>'date',
			));
		$this->addColumn('status',array(
			'header' => Mage::helper('repair')->__('Status'),
			'width'  => '100px',
			'index'  => 'status',
			'type'   =>'options',
			'options'   => array(
				'0'=>'New',
				'1'=>'In Process',
				'2'=>'Pick Up',
				'3'=>'Completed',
				'4'=>'Cancelled'),
			));
		return parent::_prepareColumns();
	}
	public function getGridUrl()
	{
	  return $this->getUrl('*/*/repairgrid', array('_current'=>true));
	}
	public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
	
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('repair_id');
		$this->getMassactionBlock()->setFormFieldName('id');
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> $this->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete', array('' => '')),
			'confirm' => $this->__('Are you sure you want to delete the selected listing(s)?')
		));
		
		$this->getMassactionBlock()->addItem('pdf', array(
             'label'    => Mage::helper('repair')->__('Export to PDF'),
             'url'      => $this->getUrl('*/*/createPdf'),
        ));
		return $this;
	}
}