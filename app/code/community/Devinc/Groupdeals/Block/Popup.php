<?php
class Devinc_Groupdeals_Block_Popup extends Mage_Core_Block_Template
{	
	//get all the active/recent/queued groupdeal product ids
    public function getProductIds()
    {
    	$attributeSetId = Mage::helper('groupdeals')->getGroupdealAttributeSetId();
    	
		//allowed deal status array
		$statusArray = array();
		$statusArray[] = Devinc_Groupdeals_Model_Source_Status::STATUS_RUNNING;	
		$statusArray[] = Devinc_Groupdeals_Model_Source_Status::STATUS_ENDED;			
    	if (Mage::getStoreConfig('groupdeals/configuration/display_upcoming')) {
			$statusArray[] = Devinc_Groupdeals_Model_Source_Status::STATUS_QUEUED;	
        } 
			
		$productIds = Mage::getResourceModel('catalog/product_collection')
			->addStoreFilter()
			->addAttributeToFilter('attribute_set_id', $attributeSetId)
			->addAttributeToFilter('groupdeal_status', array('in' => $statusArray))
			->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds())
			->getColumnValues('entity_id')
			;
		
        return $productIds;
    } 
    
    public function getCountryCollection($productIds)
    {
 		$countryCollection = Array();
 		$countryIds = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('country_id', array('neq' => ''))->addFieldToFilter('product_id', array('in' => $productIds))->setOrder('country_id', 'ASC')->getColumnValues('country_id');
		$uCountryIds = array_unique($countryIds);
		
		$i = 0;
		if (count($uCountryIds)>0) {
			foreach($uCountryIds as $countryId){
    		    $countryCollection[$i]['value'] = $countryId;
    		    $countryCollection[$i]['label'] = Mage::app()->getLocale()->getCountryTranslation($countryId);
			    $i++;
			}
		}
		
        return $countryCollection;
    } 
	
	public function getRegionCollection($countryId, $productIds)
    {
 		$regionCollection = Array();
 		$regions = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('country_id', $countryId)->addFieldToFilter('region', array('neq' => ''))->addFieldToFilter('product_id', array('in' => $productIds))->setOrder('region', 'ASC')->getColumnValues('region');
		$uRegions = array_unique($regions);
		
        return $uRegions;
    } 	
    
    public function getCityCollection($region, $productIds)
    {
 		$cityCollection = Array();
 		if (is_null($region)) {
			$cities = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('product_id', array('in' => $productIds))->setOrder('city', 'ASC')->getColumnValues('city');
		} else {
			$cities = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('region', $region)->addFieldToFilter('product_id', array('in' => $productIds))->setOrder('city', 'ASC')->getColumnValues('city');		
		}
		$uCities = array_unique($cities);	
		
        return $uCities;
    }
	
	//get session country/region/city
	public function getCurrentCrc()
    {
		$city = Mage::helper('groupdeals')->getCity();
		$region = Mage::helper('groupdeals')->getRegion();
		$currentCrcArray = Array();
					
		if (isset($city) && $city!='' && $city!='Universal') {
			$crc = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('city', $city)->addFieldToFilter('region', $region)->getFirstItem();			
			$currentCrcArray['country'] = $crc->getCountryId();
			$currentCrcArray['region'] = $region;
			$currentCrcArray['city'] = $city;
		} elseif (isset($city) && $city!='' && $city=='Universal') {
			$currentCrcArray['country'] = 'Universal';
			$currentCrcArray['region'] = 'Universal';
			$currentCrcArray['city'] = 'Universal';			
		} else {
			$currentCrcArray['country'] = '';
			$currentCrcArray['region'] = '';
			$currentCrcArray['city'] = '';
		}
		
		return $currentCrcArray;
    }
   
    //returns a city's main product url
	public function getCityUrl($_city, $_region = '', $_isMobile = false)
    {		
        $url = Mage::helper('groupdeals')->getCityUrl($_city, $_region, $_isMobile);
		
        return $url;
    } 
    
    //gift popup functions
    public function getQuote() {
    	return Mage::getSingleton('checkout/session')->getQuote();
    }
	
	public function quoteHasCoupons() {
    	$quote = $this->getQuote();
    	foreach ($quote->getAllItems() as $item) {
    		$product = Mage::getModel('catalog/product')->load($item->getProductId());
			$groupdeal = Mage::getModel('groupdeals/groupdeals')->load($item->getProductId(), 'product_id');
    		if ($groupdeal->getId() && $product->isVirtual()) {
    			return true;
    		}
    	}
    	
    	return false;
    }
	
	//subscribe popup action url	
	public function allowSubscribePopup() {		
		return Mage::getStoreConfig('groupdeals/configuration/subscribe_popup');
	}
}