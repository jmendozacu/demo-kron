<?php
class Devinc_Groupdeals_Block_Product_List extends Mage_Catalog_Block_Product_List
{
	//add breadcrumbs
    protected function _prepareLayout()
    {        
        $city = $this->getCity();
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', array(
                'label'=>Mage::helper('catalog')->__('Home'),
                'title'=>Mage::helper('catalog')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ));
            
            $breadcrumbsBlock->addCrumb('groupdeals', array(
                'label'=>Mage::helper('groupdeals')->__('Group Deals'),
                'title'=>Mage::helper('groupdeals')->__('Group Deals'),
				'link'=>"javascript:popup.showPopup();"
            ));
            
            if ($city) {
            	$breadcrumbsBlock->addCrumb('city', array(
            	    'label'=>Mage::helper('groupdeals')->__($city),
            	    'title'=>Mage::helper('groupdeals')->__($city),
            	));
            }
        }
        
        if ($this->getLayout()->getBlock('head') && $city) {
            $this->getLayout()->getBlock('head')->setTitle($this->__('%s - Current Deals', $city));
        } 
        
        return parent::_prepareLayout();
    }
    
	public function getLoadedProductCollection($includeCategoryFilter = true)
    {
		$productIds = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('city', $this->getCity())->addFieldToFilter('region', $this->getRegion())->getColumnValues('product_id');  
        $collection = Mage::getResourceModel('catalog/product_collection')
     	    ->addAttributeToSelect('*')
            ->addStoreFilter()
            ->addAttributeToFilter('entity_id', array('in' => $productIds))
			->addAttributeToFilter('groupdeal_status', Devinc_Groupdeals_Model_Source_Status::STATUS_RUNNING)
			->joinField('groupdeals_id','groupdeals/groupdeals','groupdeals_id','product_id=entity_id',null,'left')
			->joinField('position','groupdeals/groupdeals','position','product_id=entity_id',null,'left')
			->setOrder('position', 'ASC')
			->setOrder('groupdeals_id', 'DESC');
		
		//add category filter	
		if (isset($_GET['cats']) && $_GET['cats']!='' && $includeCategoryFilter) {
		    $categories = $_GET['cats'];
		    $resource = Mage::getSingleton('core/resource');
			$select  = $collection->getSelect();
			
			$select->where('(SELECT COUNT(1) FROM `'.$resource->getTableName('catalog_category_product_index').'` AS `cat_index` WHERE cat_index.product_id=e.entity_id AND cat_index.category_id IN('.$categories.') AND cat_index.store_id='.$collection->getStoreId().')');
		}
		
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        
		//update deals if they expired
		foreach ($collection as $product) {
			if (!$product->isSaleable()) {
				Mage::getModel('groupdeals/groupdeals')->refreshGroupdeal($product);
				$collection->removeItemByKey($product->getId());
			}
		}
				
        return $collection;
    }
    
	public function getGroupdeal($_productId) {	
    	$_groupdeal = Mage::getModel('groupdeals/groupdeals')->load($_productId, 'product_id');
    	
    	return $_groupdeal;
    }
    
    //returns current city; saves the current city and region in sessions
	public function getCity() {	
		if ($city = $this->getCityCode()) {	
			$crc = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('city', $city)->setOrder('crc_id', 'DESC')->getFirstItem();
			Mage::helper('groupdeals')->setCity($city);
			Mage::helper('groupdeals')->setRegion($crc->getRegion());
			return $this->getCityCode();
		} else if ($crcId = $this->getRequest()->getParam('crc', false)) {
			$crc = Mage::getModel('groupdeals/crc')->load($crcId);
			Mage::helper('groupdeals')->setCity($crc->getCity());
			Mage::helper('groupdeals')->setRegion($crc->getRegion());
			return $crc->getCity();
		} else {
			return Mage::helper('groupdeals')->getCity();
		}
	}  
    
    //returns current region
    public function getRegion() {	
		return Mage::helper('groupdeals')->getRegion();
	} 
	
	public function getMerchantDescription($_merchant, $limit) 
	{
		$storeId = Mage::app()->getStore()->getId();
		$merchantDescription = strip_tags(Mage::getModel('license/module')->getDecodeString($_merchant->getDescription(), $storeId));
		
		if (strlen($merchantDescription)>$limit) {
			return substr($merchantDescription, 0, $limit - 3).'…';
		} else {
			return $merchantDescription; 
		}
	} 
	
    public function getMerchant($_groupdeal)
    {
        return Mage::getModel('groupdeals/merchants')->load($_groupdeal->getMerchantId());
    } 
	
    public function getDiscountPercent($_product)
    {		
		$discount = ($_product->getPrice()-$_product->getFinalPrice())*100/$_product->getPrice();
		
		return number_format($discount,0).'%';
    }	
	
    public function getSoldQty($_groupdeal)
    {
		$soldQty = Mage::getModel('groupdeals/groupdeals')->getGroupdealsSoldQty($_groupdeal);	
		
        return $soldQty;
    }
    
    public function getTippingTime($_groupdeal)
    {
		$tippingTime = Mage::getModel('groupdeals/groupdeals')->getTippingTime($_groupdeal);	
		
        return $tippingTime;
    }
}
