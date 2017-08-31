<?php
class Devinc_Groupdeals_Block_Product_Recent extends Devinc_Groupdeals_Block_Product_List
{
	//add breadcrumbs
    protected function _prepareLayout()
    {
    	parent::_prepareLayout();
    	
        $city = $this->getCity();
        if ($this->getLayout()->getBlock('head') && $city) {
            $this->getLayout()->getBlock('head')->setTitle($this->__('%s - Recent Deals', $city));
        }
        
        return $this;
    }
    
	public function getLoadedProductCollection($includeCategoryFilter = true)
    {
		$productIds = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('city', $this->getCity())->addFieldToFilter('region', $this->getRegion())->getColumnValues('product_id');  
        
        $collection = Mage::getResourceModel('catalog/product_collection')
     	    ->addAttributeToSelect('*')
            ->addStoreFilter()
            ->addAttributeToFilter('entity_id', array('in' => $productIds))
			->addAttributeToFilter('groupdeal_status', Devinc_Groupdeals_Model_Source_Status::STATUS_ENDED)
			->joinField('groupdeals_id','groupdeals/groupdeals','groupdeals_id','product_id=entity_id',null,'left')
			->setOrder('groupdeal_datetime_to', 'DESC');
		
		//add category filter	
		if (isset($_GET['cats']) && $_GET['cats']!='' && $includeCategoryFilter) {
		    $categories = $_GET['cats'];
		    $resource = Mage::getSingleton('core/resource');
			$select  = $collection->getSelect();
			$select->where('(SELECT COUNT(1) FROM `'.$resource->getTableName('catalog_category_product_index').'` AS `cat_index` WHERE cat_index.product_id=e.entity_id AND cat_index.category_id IN('.$categories.') AND cat_index.store_id='.$collection->getStoreId().')');
		}
		
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
				
        return $collection;
    }
}