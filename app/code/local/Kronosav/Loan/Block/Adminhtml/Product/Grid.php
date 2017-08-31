<?php
class Kronosav_Loan_Block_Adminhtml_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
	  	parent::__construct();
		$this->setId('loanproductGrid');
		// This is the primary key of the database
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}
 
	protected function _prepareCollection()
	{
	
		$model = Mage::getModel('catalog/product');//getting product model
		$collection = $model->getCollection() //products collection
		->addAttributeToSelect('*');
     
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
         $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Add'),
                        'url'     => array(
                            'base'=>'*/*/addproduct',
                            'params'=>array('customer'=>$this->getRequest()->getParam('id')) 
                        ),
                        'field'   => 'id'
                    )
                ),
             //   'filter'    => false,
               // 'sortable'  => false,
                //'index'     => 'stores',
        ));
		
		return parent::_prepareColumns();
	}
	
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/addproduct', array('id' => $row->getId(), 'customer'=>$this->getRequest()->getParam('id')));
	}
 
	public function getGridUrl()
	{
	  return $this->getUrl('*/*/productgrid', array('_current'=>true));
	}
	protected function _prepareMassaction()
	{
	  $this->setMassactionIdField('product');
	  $this->getMassactionBlock()->setFormFieldName('entity_id');  //html name of checkbox
	  $this->getMassactionBlock()->addItem('addproduct', array(
		'label'=>  Mage::helper('loan')->__('Add Products'),
		'url'  => $this->getUrl('*/*/massAddProduct',array('id' => $this->getRequest()->getParam('id'))),
		'selected' => 'selected',
		//'confirm' => __('Are you sure?')
	  ));

	  return $this;
	  
	}
	 

 
 
}