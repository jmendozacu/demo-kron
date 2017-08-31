<?php
class Devinc_Groupdeals_Block_Product_List_Sidedeals extends Mage_Catalog_Block_Product_Abstract
{
	public function getItems()
    {        
		$productId = 0;
		if ($product = $this->getProduct()) {
			$productId = $product->getId();
		}
        $productIds = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('city', $this->getCity())->addFieldToFilter('region', $this->getRegion())->addFieldToFilter('product_id', array('neq'=>$productId))->getColumnValues('product_id');  
        
        $collection = Mage::getResourceModel('catalog/product_collection')
     	    ->addAttributeToSelect('*')
            ->addStoreFilter()
            ->addAttributeToFilter('entity_id', array('in' => $productIds))
			->addAttributeToFilter('groupdeal_status', Devinc_Groupdeals_Model_Source_Status::STATUS_RUNNING)
			->joinField('groupdeals_id','groupdeals/groupdeals','groupdeals_id','product_id=entity_id',null,'left')
			->joinField('position','groupdeals/groupdeals','position','product_id=entity_id',null,'left')
			->setOrder('position', 'ASC')
			->setOrder('groupdeals_id', 'DESC');
		
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
		
		//update deals if they expired & remove category id from url
		foreach ($collection as $product) {
			if (!$product->isSaleable()) {
				Mage::getModel('groupdeals/groupdeals')->refreshGroupdeal($product);
				$collection->removeItemByKey($product->getId());
			}
            $product->setDoNotUseCategoryId(true);
		}		
		
        return $collection;
    }
	
	public function getCity() {		
		return Mage::helper('groupdeals')->getCity();
	}
    
    public function getRegion() {	
		return Mage::helper('groupdeals')->getRegion();
	} 
	
	public function getSidealsNumber() {		
		return Mage::getStoreConfig('groupdeals/configuration/sidedeals_number');
	}
	
	public function getTitle() {
		if ($this->getProduct()) {
			return $this->__('Side deals');
		} else {
		    return $this->__($this->getCity()).$this->__(' deals');
		}
	}
	
	public function getCrcId() {
		$crcId = $this->getRequest()->getParam('crc', false);		
		if (!$crcId) {
			$helper = Mage::helper('groupdeals');
			$region = $helper->getRegion();
			$city = $helper->getCity();		
			$crc = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('region', $region)->addFieldToFilter('city', $city)->getFirstItem();
			$crcId = $crc->getId();	
		}
		
		return $crcId;
	}
	
}