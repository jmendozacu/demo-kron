<?php
class Kronosav_Repair_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('customerGrid');
		$this->setUseAjax(true);
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('customer/customer')->getCollection();
			
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	protected function _prepareColumns()
	{
		$this->addColumn('entity_id',array(
			'header' => Mage::helper('repair')->__('id'),
			'width'  => '20px',
			'index'  => 'entity_id',
			'type'   =>'number',
			));
		$this->addColumn('first_name',array(
			'header' => Mage::helper('repair')->__('Customer name'),
			'sortable'  => false,
			'filter'    => false,
			'width'  => '350px',
			'index'  => 'name',
			'renderer'=> 'Kronosav_Repair_Block_Renderer_Customer',
			));
		$this->addColumn('email',array(
			'header' => Mage::helper('repair')->__('Email'),
			'width'  => '350px',
			'index'  => 'email',
			));
		$this->addColumn('telephone',array(
			'header' => Mage::helper('repair')->__('Phone number'),
			'filter'    => false,
			'sortable'  => false,
			'width'  => '350px',
			'index'  => 'telephone',
			'renderer'=> 'Kronosav_Repair_Block_Renderer_Telephone',
			));
		return parent::_prepareColumns();
	}
	public function getGridUrl()
	{
	  return $this->getUrl('*/*/customergrid', array('_current'=>true));
	}
	public function getRowUrl($row)
    {
        return $this->getUrl('*/*/customeredit', array('id' => $row->getId()));
    }
	
}