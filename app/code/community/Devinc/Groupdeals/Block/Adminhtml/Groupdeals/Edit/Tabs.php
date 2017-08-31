<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('deal_tabs');
        $this->setDestElementId('deal_edit_form');
        $this->setTitle(Mage::helper('groupdeals')->__('Deal Information'));
    }
  
    protected function _prepareLayout()
    {	   
  		$product = $this->getProduct();	
  		if (!($attributeSetId = $product->getAttributeSetId())) {
  			$attributeSetId = $this->getRequest()->getParam('set', null);
  		}
  		
  		if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE && !($product->getTypeInstance()->getUsedProductAttributeIds())) {
  			$this->addTab('super_settings', array(
    	        'label'     => Mage::helper('catalog')->__('Configurable Product Settings'),
    	        'content'   => $this->_translateHtml(Mage::getModel('license/module')->getSuperSettingsBlock($this)),
    	        'active'    => true
    	    ));	
  		} else if ($this->getRequest()->getParam('type', null)=='catalog') {
		    $this->addTab('products_section', array(
		        'label'     => Mage::helper('groupdeals')->__('Select a Product'),
		        'title'     => Mage::helper('groupdeals')->__('Select a Product'),
		        'content'   => Mage::getModel('license/module')->getProductsBlock($this, 'groupdeals'),
		    ));
  		} else if ($attributeSetId) {	
  			if (Mage::getModel('groupdeals/merchants')->getPermission('add_edit')) {  				
  				$this->addGeneralTabBlock($attributeSetId);
  				
  				//load attribute set groups + attributes
  				$groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
  					->setAttributeSetFilter($attributeSetId)
  					->addFieldToFilter('default_id', 0);
				if (Mage::helper('groupdeals')->getMagentoVersion()>1510 && Mage::helper('groupdeals')->getMagentoVersion()<1800) {
                	$groupCollection->setSortOrder();
				}
				$groupCollection->load();
  					
  				foreach ($groupCollection as $group) {
  					$attributes = $product->getAttributes($group->getId(), true);
  					$unsetAttributesArray = array('special_from_date', 'special_to_date', 'tier_price', 'options_container', 'country_of_manufacture', 'msrp_enabled', 'msrp_display_actual_price_type', 'msrp');
  									
  					foreach ($attributes as $key => $attribute) {
  						if ($attributes[$key]->getName()=='special_price') {
  							$attributes[$key]->setIsRequired(true);
  						}
  						if(!$attribute->getIsVisible() || in_array($attributes[$key]->getName(), $unsetAttributesArray)) {
  							unset($attributes[$key]);
  						}
  					}	  
  		
  					if (count($attributes)==0) {
  						continue;
  					}
  					
  					$this->addTab('group_'.$group->getId(), array(
  					    'label'     => Mage::helper('catalog')->__($group->getAttributeGroupName()),
  					    'content'   => $this->_translateHtml($this->getLayout()->createBlock($this->getAttributeTabBlock())
  					    	->setGroup($group)
  					    	->setGroupAttributes($attributes)
  					    	->toHtml()),
  					));
  				}    
  				
  				$this->addTab('inventory', array(
  					'label'     => Mage::helper('catalog')->__('Inventory'),
  					'title'     => Mage::helper('catalog')->__('Inventory'),
  					'content'   => $this->_translateHtml($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_inventory')->toHtml()),
  				));
  		
  				/**
  				 * Don't display website tab for single mode
  				 */
  				if (!Mage::app()->isSingleStoreMode()) {
  					$this->addTab('websites', array(
  						'label'     => Mage::helper('catalog')->__('Websites'),
  						'content'   => $this->_translateHtml(Mage::getModel('license/module')->getWebsitesBlock($this)),
  					));
  				}		
  		
  				$this->addTab('categories', array(
  					'label'     => Mage::helper('catalog')->__('Categories'),
  					'url'       => Mage::getModel('license/module')->getCategoriesUrl(),
  					'class'     => 'ajax',
  				));
  		
  				//if (!Mage::getModel('groupdeals/merchants')->isMerchant()) {
    	       		$this->addTab('related', array(
    	       		    'label'     => Mage::helper('catalog')->__('Related Products'),
    	       		    'url'       => Mage::getModel('license/module')->getRelatedUrl(),
    	       		    'class'     => 'ajax',
    	       		));
  			   		
    	       		$this->addTab('upsell', array(
    	       		    'label'     => Mage::helper('catalog')->__('Up-sells'),
    	       		    'url'       => Mage::getModel('license/module')->getUpsellUrl(),
    	       		    'class'     => 'ajax',
    	       		));
  			   		
    	       		$this->addTab('crosssell', array(
    	       		    'label'     => Mage::helper('catalog')->__('Cross-sells'),
    	       		    'url'       => Mage::getModel('license/module')->getCrosssellUrl(),
    	       		    'class'     => 'ajax',
    	       		));    	        
    	        //}
    	    
    	    	$alertPriceAllow = Mage::getStoreConfig('catalog/productalert/allow_price');
            	$alertStockAllow = Mage::getStoreConfig('catalog/productalert/allow_stock');
				
            	if (($alertPriceAllow || $alertStockAllow) && !$product->isGrouped()) {
            	    $this->addTab('productalert', array(
            	        'label'     => Mage::helper('catalog')->__('Product Alerts'),
            	        'content'   => $this->_translateHtml(Mage::getModel('license/module')->getAlertsBlock($this))
            	    ));
            	}
				
            	if( $this->getRequest()->getParam('id', false) ) {
            	    if (Mage::helper('catalog')->isModuleEnabled('Mage_Review')) {
            	        if (Mage::getSingleton('admin/session')->isAllowed('admin/catalog/reviews_ratings')){
            	            $this->addTab('reviews', array(
            	                'label' => Mage::helper('catalog')->__('Product Reviews'),
    	       		   			'url'       => Mage::getModel('license/module')->getReviewsUrl(),
            	                'class' => 'ajax',
            	            ));
            	        }
            	    }
            	    if (Mage::helper('catalog')->isModuleEnabled('Mage_Tag')) {
            	        if (Mage::getSingleton('admin/session')->isAllowed('admin/catalog/tag')){
            	            $this->addTab('tags', array(
            	             'label'     => Mage::helper('catalog')->__('Product Tags'),
    	       		   		 'url'       => Mage::getModel('license/module')->getTagsUrl(),
            	             'class' 	 => 'ajax',
            	            ));
				
            	            $this->addTab('customers_tags', array(
            	                'label'     => Mage::helper('catalog')->__('Customers Tagged Product'),
    	       		   		 	'url'       => Mage::getModel('license/module')->getCustomerTagsUrl(),
            	                'class' 	=> 'ajax',
            	            ));
            	        }
            	    }
				
            	}            
  				
  				if (!$product->isGrouped()) {
  					$this->addTab('customer_options', array(
  						'label' => Mage::helper('catalog')->__('Custom Options'),
    	       		    'url'   => Mage::getModel('license/module')->getCustomOptionsUrl(),
  						'class' => 'ajax only loaded',
  					));
  				}	
  				
  				if ($product->getTypeId()=='virtual') {
  					$this->addTab('coupon_section', array(
  						'label'     => Mage::helper('groupdeals')->__('Coupon'),
  						'title'     => Mage::helper('groupdeals')->__('Coupon'),
  						'content'   => $this->_translateHtml($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_coupon')->toHtml()),
  					));	
  				}
  			}	   	
  		  
            if( $this->getRequest()->getParam('id', false) ) {
  				if (Mage::getModel('groupdeals/merchants')->getPermission('sales')) {
  					$this->addTab('orders', array(
  					    'label' => Mage::helper('catalog')->__('Orders'),
  					    'title' => Mage::helper('catalog')->__('Orders'),
  					    'url'   => $this->getUrl('*/*/orders', array('_current' => true)),
  					    'class' => 'ajax',
  					));
  				}
  				  
  				if (Mage::getModel('groupdeals/merchants')->getPermission('add_edit')) {
  					$this->addTab('notifications_section', array(
  						'label'     => Mage::helper('groupdeals')->__('Notifications'),
  						'title'     => Mage::helper('groupdeals')->__('Notifications'),
  						'content'   => $this->_translateHtml($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_notifications')->toHtml()),
  					));	
  				}
  			}
  		} else {
  			$this->addTab('set', array(
  				'label'     => Mage::helper('catalog')->__('Settings'),
  				'content'   => $this->_translateHtml($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_settings')->toHtml()),
  				'active'    => true
  			));
  		}
    }
    
    public function addGeneralTabBlock($attributeSetId) {
        $generalGroup = Mage::getResourceModel('eav/entity_attribute_group_collection')
  	    	->setAttributeSetFilter($attributeSetId)
  	        ->addFieldToFilter('default_id', 1)
  	        ->load()
  	        ->getFirstItem();
  	        
  	    $product = $this->getProduct();
  	    $attributes = $product->getAttributes($generalGroup->getId(), true);
  	    $unsetAttributesArray = array('name', 'description', 'short_description', 'news_from_date', 'news_to_date', 'groupdeal_datetime_from', 'groupdeal_datetime_to', 'is_imported', 'groupdeal_status');
  	    
  	    //if logged in as merchant & require administrator approval, disable status field
  	    $requireAdminApproval = Mage::getModel('groupdeals/merchants')->getPermission('approve');
  	    if ($requireAdminApproval) {
  	    	$unsetAttributesArray[] = 'status';
  	    }	
  	    
  	    foreach ($attributes as $key => $attribute) {
  	    	if ($attributes[$key]->getName()=='name') {
  	    		$nameAttribute[$key] = $attribute;
  	    	}
  	        if(!$attribute->getIsVisible() || in_array($attributes[$key]->getName(), $unsetAttributesArray)) {
  	        	unset($attributes[$key]);
  	        }
  	    }
  	    
  	    return $this->addTab('group_'.$generalGroup->getId(), array(
  		    'label'     => Mage::helper('catalog')->__($generalGroup->getAttributeGroupName()),
  		    'content'   => $this->_translateHtml($this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals_edit_tab_general')
  		    	->setGroup($generalGroup)
  		    	->setGeneralAttributes($attributes)
  		    	->setNameAttribute($nameAttribute)
  		    	->toHtml()),
  		));
    }

    /**
     * Translate html content
     *
     * @param string $html
     * @return string
     */
    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }

}