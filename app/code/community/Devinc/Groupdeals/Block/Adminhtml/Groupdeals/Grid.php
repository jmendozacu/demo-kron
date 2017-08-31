<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	const STATUS_RUNNING = Devinc_Groupdeals_Model_Source_Status::STATUS_RUNNING;
	const STATUS_DISABLED = Devinc_Groupdeals_Model_Source_Status::STATUS_DISABLED;
	const STATUS_ENDED = Devinc_Groupdeals_Model_Source_Status::STATUS_ENDED;
	const STATUS_QUEUED = Devinc_Groupdeals_Model_Source_Status::STATUS_QUEUED;
	const STATUS_PENDING = Devinc_Groupdeals_Model_Source_Status::STATUS_PENDING;
	
    public function __construct()
    {
        parent::__construct();
        $this->setId('groupdealsGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true); 
    }
  
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
  	    $dealProductIds = Mage::getModel('groupdeals/groupdeals')->getCollection()->getColumnValues('product_id'); 
   	    
        $collection = Mage::getModel('catalog/product')->getCollection()		
                ->addAttributeToSelect('entity_id')		
                ->addAttributeToSelect('name')	
                ->addAttributeToSelect('type_id')	
                ->addAttributeToSelect('special_price')			
                ->addAttributeToSelect('groupdeal_datetime_from')	
                ->addAttributeToSelect('groupdeal_datetime_to')		
                ->addAttributeToSelect('groupdeal_status')	              		
  	    		->addStoreFilter($store)
  	    		->joinField('groupdeals_id','groupdeals/groupdeals','groupdeals_id','product_id=entity_id',null,'left')
  	    		->joinField('position','groupdeals/groupdeals','position','product_id=entity_id',null,'left')
  	    		->joinField('merchant_id','groupdeals/groupdeals','merchant_id','product_id=entity_id',null,'left') 		
  	    		->addAttributeToFilter('entity_id', array('in' => $dealProductIds))
  	    		;

  	    //if logged in as merchant load only it's deals   
  	    if ($merchant = Mage::getModel('groupdeals/merchants')->isMerchant()) {
  	    	   $collection->addFieldToFilter('merchant_id', $merchant->getId());
  	    }	
   	    
  	    $defaultSort = Mage::getSingleton('adminhtml/session')->getData('groupdealsGridsort');
   	    $sort = $this->getRequest()->getParam('sort', '');
  	    if ($sort=='' && $defaultSort=='') {
  	    	  $collection->setOrder('groupdeal_status', 'asc')->setOrder('position', 'asc')->setOrder('groupdeals_id', 'desc');
  	    }  	  
  	    
        $this->setCollection($collection);
  	    parent::_prepareCollection();
  	    
  	    $this->getCollection()->addWebsiteNamesToResult();
  	    $this->addCitiesToResult($this->getCollection());
  	    
  	    return $this;
    }
    
    public function addCitiesToResult($_collection)
    {
        $productCities = array();
        foreach ($_collection as $product) {
            $productCities[$product->getId()] = array();
        }
        
        foreach ($_collection as $product) {
        	$crcCollection = Mage::getModel('groupdeals/crc')->getCrcCollection($product->getGroupdealsId());
        	foreach ($crcCollection as $item) {
	            $productCities[$product->getId()][] = $item->getId();
	        }
        }

        foreach ($_collection as $product) {
            if (isset($productCities[$product->getId()])) {
                $product->setData('cities', $productCities[$product->getId()]);
            }
        }
        return $_collection;
    }
      
    protected function _addColumnFilterToCollection($column)
    {
  	  if ($this->getCollection()) {
  		  if ($column->getId() == 'websites') {
  			  $this->getCollection()->joinField('websites',
  				'catalog/product_website',
  				'website_id',
  				'product_id=entity_id',
  				null,
  				'left');
  		  }
  		  
  		  if ($column->getId() == 'cities') {
  			  $this->getCollection()->joinField('cities',
  				'groupdeals/crc',
  				'crc_id',
  				'groupdeals_id=groupdeals_id',
  				null,
  				'left');
  		  }
  		  
  		  if ($column->getId() == 'sold_qty') {
  			  $this->getCollection()->joinField('sold_qty',
  				'groupdeals/groupdeals',
  				'sold_qty',
  				'product_id=entity_id',
  				null,
  				'left');
  		  }
  	  }
  	  return parent::_addColumnFilterToCollection($column);
    }
    
    protected function _prepareColumns()
    {  		
  	    $this->addColumn('name', array( 
            'header'    => Mage::helper('groupdeals')->__('Name'),
            'align'     => 'left',
            'index'     => 'name',
            'type'      => 'text',
        ));       
  
  	    $this->addColumn('type_id', array(
            'header'    => Mage::helper('groupdeals')->__('Type'),
            'align'     => 'left',
            'index'     => 'type_id',
            'width'     => '70px',
            'type'      => 'options',
            'options'   => array(
                'virtual' 	   => 'Virtual Product (Coupon)',
                'simple' 	   => 'Simple Product',
                'configurable' => 'Configurable Product',
            ),
            'renderer'  => 'groupdeals/adminhtml_groupdeals_grid_renderer_type',
        ));  
  	  
        $store = $this->_getStore();  
        	  
  	    $this->addColumn('special_price', array( 
            'header'        => Mage::helper('groupdeals')->__('Special Price'),
            'align'         => 'left',
            'index'         => 'special_price',
            'width'         => '50px',
            'currency_code' => $store->getBaseCurrency()->getCode(),
            'type'      	=> 'price',
        ));     
  		
  	    $this->addColumn('target', array( 
            'header'    => Mage::helper('groupdeals')->__('Purchased/Target'),
            'align'     => 'left',
            'index'     => 'groupdeals_id',
            'width'     => '50px',
            'type'      => 'text',
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'groupdeals/adminhtml_groupdeals_grid_renderer_target',
        ));  
  	  
  	    $this->addColumn('groupdeal_datetime_from', array(
            'header'    => Mage::helper('groupdeals')->__('Date/Time From'),
            'align'     => 'left',
            'width'     => '135px',
            'type'      => 'date',
            'format'    => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
            'gmtoffset' => false,
            'default'   => '',
            'index'     => 'groupdeal_datetime_from',
        ));	
  	  
  	    $this->addColumn('groupdeal_datetime_to', array(
            'header'    => Mage::helper('groupdeals')->__('Date/Time To'),
            'align'     => 'left',
            'width'     => '135px',
            'type'      => 'date',
            'format'    => Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM),
            'gmtoffset' => false,
            'default'   => '',
            'index'     => 'groupdeal_datetime_to',
        ));	  
  	 	    	  
  	    $this->addColumn('cities', array( 
  	    	'header'    => Mage::helper('groupdeals')->__('City'),
  	    	'align'     => 'left',
  	    	'width'     => '100px',
  	    	'index'     => 'cities',
            'sortable'  => false,
  	    	'type'      => 'options',
  	    	'options'   => Mage::getModel('groupdeals/crc')->getCollection()->toOptionHash(),
  	    ));    
  	    
  	    if (!Mage::app()->isSingleStoreMode()) {
  	    	$this->addColumn('websites',
  	    		array(
  	    			'header'=> Mage::helper('groupdeals')->__('Websites'),
  	    			'width' => '100px',
  	    			'sortable'  => false,
  	    			'index'     => 'websites',
  	    			'type'      => 'options',
  	    			'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
  	    	));
  	    }
  	  
        $this->addColumn('groupdeal_status', array(
            'header'    => Mage::helper('groupdeals')->__('Status'),
            'align'     => 'left',
            'index'     => 'groupdeal_status',
            'width'     => '100px',
            'type'      => 'options',
            'options'   => array(
                self::STATUS_QUEUED => 'Queued',
                self::STATUS_RUNNING => 'Running',
                self::STATUS_ENDED => 'Ended',
                self::STATUS_DISABLED => 'Disabled',
                self::STATUS_PENDING => 'Pending Approval',
            ),
            'renderer'  => 'groupdeals/adminhtml_groupdeals_grid_renderer_status',
        )); 
  	  
  	    $action_array = array();  	  
  	    if (Mage::getModel('groupdeals/merchants')->getPermission('add_edit') || Mage::getModel('groupdeals/merchants')->getPermission('sales')) {
  	    	$action_array[] = array(
  	    		'caption'   => Mage::helper('groupdeals')->__('Edit'),
  	    		'url'       => array('base'=> '*/*/columnEdit'),
  	    		'field'     => 'groupdeals_id'
  	    	);	
  	    }
  	    
  	    if (Mage::getModel('groupdeals/merchants')->getPermission('delete')) {
  	    	$action_array[] = array(
  	    		'caption'   => Mage::helper('groupdeals')->__('Delete'),
  	    		'url'       => array('base'=> '*/*/delete'),
  	    		'field'     => 'groupdeals_id',
  	    		'confirm'  => Mage::helper('groupdeals')->__('Are you sure you want to delete this deal?')
  	    	);
  	    }  	  
  	    
  	    
        $this->addColumn('position', array(
            'header'    => Mage::helper('catalog')->__('Position'),
            'type'      => 'number',
            'index'     => 'position',
            'editable'  => false
        ));
  	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('groupdeals')->__('Action'),
                'type'      => 'action',
                'getter'    => 'getGroupdealsId',
  		        'width'     => '70px',
                'actions'   => $action_array,
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
                'width'     => '100px',
        ));
  		
  		$this->addExportType('*/*/exportCsv', Mage::helper('groupdeals')->__('CSV'));
  	  
        return parent::_prepareColumns();
    }
  
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');
  
  		if (Mage::getModel('groupdeals/merchants')->getPermission('delete')) {
  			$this->getMassactionBlock()->addItem('delete', array(
  				 'label'    => Mage::helper('groupdeals')->__('Delete'),
  				 'url'      => $this->getUrl('*/*/massDelete'),
  				 'confirm'  => Mage::helper('groupdeals')->__('Are you sure you want to delete these deals?')
  			));
  		}
  
        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();
        array_unshift($statuses, array('label'=>'', 'value'=>''));
        
  		if (Mage::getModel('groupdeals/merchants')->getPermission('add_edit') && Mage::getModel('groupdeals/merchants')->getPermission('approve')) {		
  			$this->getMassactionBlock()->addItem('status', array(
  				'label'=> Mage::helper('groupdeals')->__('Change status'),
  				'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
  				'additional' => array(
  					'visibility'  => array(
  						'name'   => 'status',
  						'type'   => 'select',
  						'class'  => 'required-entry',
  						'label'  => Mage::helper('groupdeals')->__('Status'),
  						'values' => $statuses
  					)
  				)
  			));
  		}
  		
        return $this;
    }
  
    protected function _getStore()
    {
          $storeId = (int) $this->getRequest()->getParam('store', 0);
          return Mage::app()->getStore($storeId);
    }
	
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
  	
    public function getRowUrl($row)
    {
        if (!Mage::getModel('groupdeals/merchants')->getPermission('add_edit') && !Mage::getModel('groupdeals/merchants')->getPermission('sales')) {
  		    return false;
  	  } else {
  			return $this->getUrl('*/*/edit', array('groupdeals_id' => $row->getGroupdealsId(), 'id' => $row->getId()));
  	  }
    }

}