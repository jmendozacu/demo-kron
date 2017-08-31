<?php

class Devinc_Groupdeals_Model_Groupdeals extends Mage_Core_Model_Abstract
{	
	const STATUS_RUNNING = Devinc_Groupdeals_Model_Source_Status::STATUS_RUNNING;
	const STATUS_DISABLED = Devinc_Groupdeals_Model_Source_Status::STATUS_DISABLED;
	const STATUS_ENDED = Devinc_Groupdeals_Model_Source_Status::STATUS_ENDED;
	const STATUS_QUEUED = Devinc_Groupdeals_Model_Source_Status::STATUS_QUEUED;
	const STATUS_PENDING = Devinc_Groupdeals_Model_Source_Status::STATUS_PENDING;
					
	public function _construct()
	{
		parent::_construct();
		$this->_init('groupdeals/groupdeals');				
	} 
	
	//returns the total sold qty of the deal, excluding canceled orders
	public function getGroupdealsSoldQty($_groupdeal, $_customerEmail = null) {
		if (is_null($_customerEmail)) {
			$report = $this->getOrderedQty($_groupdeal->getProductId());	
			//$report = Mage::getResourceModel('reports/product_collection')->addOrderedQty()->addAttributeToFilter('entity_id', $_groupdeal->getProductId())->getFirstItem()->getOrderedQty();
		} else {
			$report = $this->getCustomerOrderedQty($_customerEmail, $_groupdeal->getProductId());		
		}
		
		$soldQty = number_format($report, 0);
		
		return $soldQty;
	}
	
	public function getOrderedQty($_productId)
    {
        $adapter              = Mage::getResourceModel('reports/product_collection')->getConnection();
        $compositeTypeIds     = array();
        $compositeTypeIds[]   = 'grouped';
        //$compositeTypeIds[]   = 'bundle';
        $orderTableAliasName  = $adapter->quoteIdentifier('order');

        $orderJoinCondition   = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state <> ?", Mage_Sales_Model_Order::STATE_CANCELED),

        );

        $productJoinCondition = array(
            $adapter->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
            'e.entity_id = order_items.product_id',
            $adapter->quoteInto('e.entity_type_id = ?', Mage::getResourceModel('reports/product_collection')->getProductEntityTypeId())
        );

        $select = Mage::getResourceModel('reports/product_collection')->getSelect()->reset()
            ->from(
                array('order_items' => Mage::getResourceModel('reports/product_collection')->getTable('sales/order_item')),
                array(
                    'ordered_qty' => 'SUM(order_items.qty_ordered)',
                    'order_items_name' => 'order_items.name'
                ))
            ->joinInner(
                array('order' => Mage::getResourceModel('reports/product_collection')->getTable('sales/order')),
                implode(' AND ', $orderJoinCondition),
                array())
            ->joinLeft(
                array('e' => Mage::getResourceModel('reports/product_collection')->getProductEntityTableName()),
                implode(' AND ', $productJoinCondition),
                array(
                    'entity_id' => 'order_items.product_id',
                    'entity_type_id' => 'e.entity_type_id',
                    'attribute_set_id' => 'e.attribute_set_id',
                    'type_id' => 'e.type_id',
                    'sku' => 'e.sku',
                    'has_options' => 'e.has_options',
                    'required_options' => 'e.required_options',
                    'created_at' => 'e.created_at',
                    'updated_at' => 'e.updated_at'
                ))
            ->where('parent_item_id IS NULL')
            ->where('e.entity_id = '.$_productId)
            ->group('order_items.product_id')
            ->having('SUM(order_items.qty_ordered) > ?', 0);
            
        $data = $adapter->fetchAll($select); 
           
        if (isset($data[0])) {
	        return $data[0]['ordered_qty'];
	    } else {
	    	return 0;
	    }
	     
    }
	
    public function getCustomerOrderedQty($_customerEmail, $_productId)
    {
        $adapter              = Mage::getResourceModel('reports/product_collection')->getConnection();
        $compositeTypeIds     = array();
        $compositeTypeIds[]   = 'grouped';
        //$compositeTypeIds[]   = 'bundle';
        $orderTableAliasName  = $adapter->quoteIdentifier('order');

        $orderJoinCondition   = array(
            $orderTableAliasName . '.entity_id = order_items.order_id',
            $adapter->quoteInto("{$orderTableAliasName}.state <> ?", Mage_Sales_Model_Order::STATE_CANCELED),
            $adapter->quoteInto("{$orderTableAliasName}.customer_email = ?", $_customerEmail),
        );

        $productJoinCondition = array(
            $adapter->quoteInto('(e.type_id NOT IN (?))', $compositeTypeIds),
            'e.entity_id = order_items.product_id',
            $adapter->quoteInto('e.entity_type_id = ?', Mage::getResourceModel('reports/product_collection')->getProductEntityTypeId())
        );

        $select = Mage::getResourceModel('reports/product_collection')->getSelect()->reset()
            ->from(
                array('order_items' => Mage::getResourceModel('reports/product_collection')->getTable('sales/order_item')),
                array(
                    'ordered_qty' => 'SUM(order_items.qty_ordered)',
                    'order_items_name' => 'order_items.name'
                ))
            ->joinInner(
                array('order' => Mage::getResourceModel('reports/product_collection')->getTable('sales/order')),
                implode(' AND ', $orderJoinCondition),
                array())
            ->joinLeft(
                array('e' => Mage::getResourceModel('reports/product_collection')->getProductEntityTableName()),
                implode(' AND ', $productJoinCondition),
                array(
                    'entity_id' => 'order_items.product_id',
                    'entity_type_id' => 'e.entity_type_id',
                    'attribute_set_id' => 'e.attribute_set_id',
                    'type_id' => 'e.type_id',
                    'sku' => 'e.sku',
                    'has_options' => 'e.has_options',
                    'required_options' => 'e.required_options',
                    'created_at' => 'e.created_at',
                    'updated_at' => 'e.updated_at'
                ))
            ->where('parent_item_id IS NULL')
            ->where('e.entity_id = '.$_productId)
            ->group('order_items.product_id')
            ->having('SUM(order_items.qty_ordered) > ?', 0);
            
        $data = $adapter->fetchAll($select); 
           
        if (isset($data[0])) {
	        return $data[0]['ordered_qty'];
	    } else {
	    	return 0;
	    }
    }
    
    public function getTippingTime($_groupdeal)
    {
		//$from = Mage::helper('groupdeals')->convertDateToUtc($_product->getGroupdealDatetimeFrom());
		//$to = Mage::helper('groupdeals')->convertDateToUtc($_product->getGroupdealDatetimeTo());
		$orderItems = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('product_id', $_groupdeal->getProductId());
		
		$tippingTime = '';		
		$target = $_groupdeal->getMinimumQty();
		$soldQty = 0;		
		if (count($orderItems)>0) {
			foreach($orderItems as $item) {
				$orderStatus = Mage::getModel('sales/order')->load($item->getOrderId())->getStatus();
				if ($orderStatus != 'canceled') {
					$soldQty = $soldQty+(int)$item->getQtyOrdered();	
					if ($soldQty>=$target) {					
					    $tippingTime = Mage::getModel('core/date')->date('h:iA', strtotime($item->getCreatedAt()));
					    break;
					}
					if ($tippingTime!='') {
						break;
					}
				}
			}	
		}
		
        return $tippingTime;
    }
	
	//update url rewrite	
	public function updateUrlRewrite($product, $groupdeal) {			 
		$productId = $product->getId();
		$groupdealId = $groupdeal->getId();				
		$stores = Mage::app()->getStores();	
			
		foreach ($stores as $_eachStoreId => $val) 
		{
			$store = Mage::app()->getStore($_eachStoreId);			
			if ($store->getRootCategoryId()) {
				$_storeId = $store->getId();
								
				Mage::getSingleton('catalog/url')->refreshProductRewrite($productId, $_storeId);
				$productUrlRewrite = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('store_id', $_storeId)->addFieldToFilter('target_path', 'catalog/product/view/id/'.$productId)->getFirstItem();					
				if ($productUrlRewrite->getId()) {
					$productUrlRewrite->setTargetPath('groupdeals/product/view/id/'.$productId.'/groupdeals_id/'.$groupdealId)->save();
				}		
				
				if (count($product->getCategoryIds())>0) {
					foreach ($product->getCategoryIds() as $categoryId) {
						$categoryUrlRewrite = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('store_id', $_storeId)->addFieldToFilter('target_path', 'catalog/product/view/id/'.$productId.'/category/'.$categoryId)->getFirstItem();					
						if ($categoryUrlRewrite->getId()) {
							$categoryUrlRewrite->setTargetPath('groupdeals/product/view/id/'.$productId.'/groupdeals_id/'.$groupdealId.'/category/'.$categoryId)->save();
						}	
					}										 
				}											 
			}										 
		} 	
	}
	
	//returns the main store id of each website
	public function getMainStoreIds($includeAdmin = true) {	
		$storeIds = array();
		if ($includeAdmin) {
			$storeIds = array(0);
		}
		
		$websiteCollection = Mage::getModel('core/website')->getCollection();
				
		if (count($websiteCollection)) {	
		    foreach ($websiteCollection as $website) 
		    {	
		    	$storeId = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $website->getId())->addFieldToFilter('is_active', 1)->setOrder('store_id', 'asc')->getFirstItem()->getId();
		    	//verify if at least one store is active from the website
		    	if ($storeId) {
			    	$storeIds[] = $storeId;
			    }
		    }		
		}
		
		return $storeIds;
	}
	
	//returns the store ids of a product
	public function getProductStoreIds($_product) {	
		if (count($_product->getWebsiteIds())) {	
			$storeIds = array();
			$websiteCollection = Mage::getModel('core/website')->getCollection()->addFieldToFilter('website_id', array('in', $_product->getWebsiteIds()));	
					
			if (count($websiteCollection)) {	
			    foreach ($websiteCollection as $website) 
			    {	
			    	$storeCollection = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $website->getId())->addFieldToFilter('is_active', 1)->setOrder('store_id', 'asc');
			    	if (count($storeCollection)) {	
			    		foreach ($storeCollection as $store) 
			    		{
				    		$storeIds[] = $store->getId();			    		
				    	}
				    }
			    }		
			}
		} else {
			$storeIds = '';
		}
		
		return $storeIds;
	}
	
	//refresh the status of all the groupdeals
	public function refreshGroupdeals() {
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$dealProductIds = Mage::getModel('groupdeals/groupdeals')->getCollection()->getColumnValues('product_id');
				
		if (count($dealProductIds)>0) {	
			$storeIds = $this->getMainStoreIds();
			foreach ($storeIds as $storeId) {	
				foreach ($dealProductIds as $productId) {
					$product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId);
			    	$this->refreshGroupdeal($product, $storeId);
			    }
			}				
		}
	}	
	
	//updates groupdeal status
	public function refreshGroupdeal($_product, $_storeId = null) {
		if (is_null($_storeId)) {
			$storeIds = $this->getMainStoreIds();
		} else {
			$storeIds = array($_storeId);
		}
		
		foreach ($storeIds as $storeId) {	
			$websiteId = Mage::getModel('core/store')->load($storeId)->getWebsiteId();
			if (in_array($websiteId, $_product->getWebsiteIds()) || $storeId==0) {	
			    if ($attributesArray = $this->checkDealStatus($_product, $storeId)) {	
			    	Mage::getSingleton('catalog/product_action')->updateAttributes(array($_product->getId()), array('groupdeal_status' => $attributesArray['status']), $storeId);
			    }	
			} else {
			    Mage::getSingleton('catalog/product_action')->updateAttributes(array($_product->getId()), array('groupdeal_status' => self::STATUS_DISABLED), $storeId);
			}
		}
	}
	
	//compare original groupdeal status with current to see if it requires refresh
	//note: a groupdeal's status changes depending on the products status, datetime from/to, stock
	public function checkDealStatus($_product, $_storeId) {
		$origGroupdealStatus = $_product->getGroupdealStatus();
		$stockItem = $_product->getStockItem();	
					
		// get store datetime
		$helper = Mage::helper('groupdeals');
		$currentDateTime = $helper->getCurrentDateTime($_storeId);
		
		//chech if pending approval
		if ($_product->getGroupdealStatus()!=self::STATUS_PENDING) {
			$groupdealStatus = self::STATUS_ENDED;
			//check if disabled
			if ($_product->getStatus()!=2) {
				//check if running
				if ($currentDateTime>=$_product->getGroupdealDatetimeFrom() && $currentDateTime<=$_product->getGroupdealDatetimeTo()) {
					if ($stockItem->getIsInStock()) {								
						$groupdealStatus = self::STATUS_RUNNING;
					} else {				
						$groupdealStatus = self::STATUS_ENDED;
					}
				//check if queued
				} elseif ($currentDateTime<=$_product->getGroupdealDatetimeFrom()) {							
					$groupdealStatus = self::STATUS_QUEUED;
				//check if ended
				} elseif ($currentDateTime>=$_product->getGroupdealDatetimeTo()) {
					$groupdealStatus = self::STATUS_ENDED;
				}
			} else {
				$groupdealStatus = self::STATUS_DISABLED;
			}		
		} else {
			$groupdealStatus = $_product->getGroupdealStatus();
		}
		
		if ($origGroupdealStatus==$groupdealStatus) {
			return false;
		} else {
			return array('status' => $groupdealStatus);
		}
	}
	
	//installation functions
	public function addGroupdealAttributeSet($installer)
	{ 
		$entityTypeCode = 'catalog_product';
		$entityTypeId = $installer->getEntityTypeId($entityTypeCode);
		$attributeSetName = 'Group Deal';
		$skeletonSet = $installer->getDefaultAttributeSetId($entityTypeId);
		
		$setId = $installer->getAttributeSet($entityTypeId, $attributeSetName, 'attribute_set_id');
		if (!$setId) {
			$model  = Mage::getModel('eav/entity_attribute_set')->setEntityTypeId($entityTypeId);
			$model->setAttributeSetName(trim($attributeSetName));
			$model->validate();
			$model->save();
			$model->initFromSkeleton($skeletonSet);
			$model->save();
		}		
		
		$attributeSetId = $installer->getAttributeSetId($entityTypeCode, $attributeSetName);
		
		//save groupdeals attribute set id in config
		Mage::getModel('core/config')->saveConfig('groupdeals/attribute_set_id', $attributeSetId, 'default', 0);	
				
		//add groupdeal attributes		
		$productTypes = array('simple', 'virtual', 'configurable', 'bundle');	 
		
		//add fine print attribute
		$data['groupdeal_fineprint'] = array(
		            'frontend_label'  				=> 'Fine Print',
					'attribute_code'  				=> 'groupdeal_fineprint',
		            'is_global'       				=> 0,    
		            'frontend_input'  				=> 'textarea',
		            'default_value'   				=> '',         
		            'is_unique'       				=> 0,
		            'is_required'     				=> 1,
					'apply_to'        				=> $productTypes,
		            'is_searchable'   				=> 1,      
		            'is_visible_in_advanced_search' => 1,              
		            'is_comparable'     			=> 1,         
		            'is_wysiwyg_enabled'     		=> 1,  
		            'is_user_defined'				=> 1, 
		        );
		if (!$installer->getAttributeId($entityTypeCode, $data['groupdeal_fineprint']['attribute_code'])) {
			$this->addAttribute($data['groupdeal_fineprint'], $entityTypeId);
			$installer->addAttributeToSet($entityTypeCode, $attributeSetId, 'General', $data['groupdeal_fineprint']['attribute_code']);
		}
		
		//add highlights attribute
		$data['groupdeal_highlights'] = array(
		            'frontend_label'  				=> 'Highlights',
					'attribute_code'  				=> 'groupdeal_highlights',
		            'is_global'       				=> 0,    
		            'frontend_input'  				=> 'textarea',
		            'default_value'   				=> '',         
		            'is_unique'       				=> 0,
		            'is_required'     				=> 1,
					'apply_to'        				=> $productTypes,
		            'is_searchable'   				=> 1,      
		            'is_visible_in_advanced_search' => 1,              
		            'is_comparable'     			=> 1,         
		            'is_wysiwyg_enabled'     		=> 1,   
		            'is_user_defined'				=> 1,
		        );
		if (!$installer->getAttributeId($entityTypeCode, $data['groupdeal_highlights']['attribute_code'])) {
			$this->addAttribute($data['groupdeal_highlights'], $entityTypeId);
			$installer->addAttributeToSet($entityTypeCode, $attributeSetId, 'General', $data['groupdeal_highlights']['attribute_code']);
		}
		
		//add status attribute
		$data['groupdeal_status'] = array(
		            'frontend_label'  				=> 'Deal Status',
					'attribute_code'  				=> 'groupdeal_status',
		            'is_global'       				=> 2,    
		            'frontend_input'  				=> 'select',
		            'default_value'   				=> '',         
		            'is_unique'       				=> 0,
		            'is_required'     				=> 1,
					'apply_to'        				=> $productTypes,
		            'is_searchable'   				=> 0,    
            		'used_in_product_listing'		=> 1,     
		            'is_visible_in_advanced_search' => 0,              
		            'is_comparable'     			=> 0,         
		            'is_wysiwyg_enabled'     		=> 0,  
		            'source_model'    	 			=> 'groupdeals/source_status', 
		            'is_user_defined'				=> 0,
		        );
		if (!$installer->getAttributeId($entityTypeCode, $data['groupdeal_status']['attribute_code'])) {
			$this->addAttribute($data['groupdeal_status'], $entityTypeId);
			$installer->addAttributeToSet($entityTypeCode, $attributeSetId, 'General', $data['groupdeal_status']['attribute_code']);
		}
		
		//clear translation cache because attribute labels are stored in translation
		Mage::app()->cleanCache(array(Mage_Core_Model_Translate::CACHE_TAG));
	}
	
	function addAttribute($data, $entityTypeId)
	{ 
		$model = Mage::getModel('catalog/resource_eav_attribute');
        $helper = Mage::helper('catalog/product');
            
        if (!isset($data['source_model'])) {
        	//$data['source_model'] = $helper->getAttributeSourceModelByInputType($data['frontend_input']);
        	$data['source_model'] = null;
        }
        
        if (!isset($data['backend_model'])) {
        	//$data['backend_model'] = $helper->getAttributeBackendModelByInputType($data['frontend_input']);
        	$data['backend_model'] = null;
        }
        
        if (!isset($data['is_configurable'])) {
            $data['is_configurable'] = 0;
        }
        if (!isset($data['is_filterable'])) {
            $data['is_filterable'] = 0;
        }
        if (!isset($data['is_filterable_in_search'])) {
            $data['is_filterable_in_search'] = 0;
        }

        $data['backend_type'] = $model->getBackendTypeByInput($data['frontend_input']);

        if(!isset($data['apply_to'])) {
            $data['apply_to'] = array();
        }

        $model->addData($data);

        $model->setEntityTypeId($entityTypeId);
        $model->setIsUserDefined($data['is_user_defined']);
        $model->save();
        $model->setSourceModel($data['source_model'])->save();
    }
}