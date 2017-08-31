<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Tab_Notifications extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('notificationsGrid');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);
        $this->setVarNameFilter('notifications_filter');
    }
    
    protected function _prepareCollection()
    {
    	$groupdealId = Mage::registry('groupdeals_data')->getId();
    	$cityArray = Mage::getModel('groupdeals/crc')->getCitiesArray($groupdealId);    
    	
    	//get the store ids in which the product is available	
    	$product = Mage::registry('current_product');
    	$storeIds = Mage::getModel('groupdeals/groupdeals')->getProductStoreIds($product);	
    	  
    	$subscriberCollection = Mage::getModel('groupdeals/subscribers')->getCollection()->addFieldToFilter('city', array('in' => $cityArray))->addFieldToFilter('store_id', array('in' => $storeIds));   	
    	
        $this->setCollection($subscriberCollection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {	
    
    	$this->addColumn('email', array( 
        	'header'    => Mage::helper('groupdeals')->__('Subscriber Email'),
            'align'     => 'left',
            'index'     => 'email',
            'type'      => 'text',
        ));       
    	  
    	if (!Mage::app()->isSingleStoreMode()) {
    	    $this->addColumn('store_id', array(
    	  	    'header'    => Mage::helper('groupdeals')->__('Subscribed to (Store)'),
    	  	    'index'     => 'store_id',
    	  	    'type'      => 'store',
    	        'width'     =>'200px',
    	  	    'store_view'=> true,
    	  	    'display_deleted' => false,
    	    ));
    	}
    	  
        $this->addColumn('new_deal', array(
            'header'    => Mage::helper('groupdeals')->__('New Deal Notification'),
            'align'     => 'left',
            'index'     => 'subscriber_id',
    		'filter' => false,
            'width'     => '100px',
            'type'      => 'options',
            'options'   => array(
            	0 => 'Sent',
                1 => 'Pending',
                2 => 'Not Sent',
            ),
            'renderer'  => 'groupdeals/adminhtml_groupdeals_edit_renderer_newdeal',
        ));
    	  
        $this->addColumn('limit_met', array(
            'header'    => Mage::helper('groupdeals')->__('Target Met Notification'),
            'align'     => 'left',
            'index'     => 'subscriber_id',
    		'filter' => false,
            'width'     => '100px',
            'type'      => 'options',
            'options'   => array(
                0 => 'Sent',
                1 => 'Pending',
                2 => 'Not Sent',
            ),
            'renderer'  => 'groupdeals/adminhtml_groupdeals_edit_renderer_limitmet',
        ));
    	  
        $this->addColumn('deal_over', array(
            'header'    => Mage::helper('groupdeals')->__('Deal Over Notification'),
            'align'     => 'left',
            'index'     => 'subscriber_id',
    		'filter' => false,
            'width'     => '100px',
            'type'      => 'options',
            'options'   => array(
                0 => 'Sent',
                1 => 'Pending',
                2 => 'Not Sent',
            ),
            'renderer'  => 'groupdeals/adminhtml_groupdeals_edit_renderer_dealover',
        ));
    	    
        return parent::_prepareColumns();
    }
    	
    public function getGridUrl()
    {
        return $this->getUrl('*/*/notifications', array('_current'=>true));
    }

}