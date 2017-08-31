<?php
class Devinc_Groupdeals_Block_Product_List_Upcoming extends Devinc_Groupdeals_Block_Product_List_Sidedeals
{    
	public function getItems()
    {        
        $productIds = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('city', $this->getCity())->addFieldToFilter('region', $this->getRegion())->getColumnValues('product_id'); 
        
        $collection = Mage::getResourceModel('catalog/product_collection')
     	    ->addAttributeToSelect('*')
            ->addStoreFilter()
            ->addAttributeToFilter('entity_id', array('in' => $productIds))
			->addAttributeToFilter('groupdeal_status', Devinc_Groupdeals_Model_Source_Status::STATUS_QUEUED)
			->joinField('groupdeals_id','groupdeals/groupdeals','groupdeals_id','product_id=entity_id',null,'left')
			->setOrder('groupdeals_id', 'DESC');
		
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
				 
        return $collection;
    }
	
	public function getDisplayUpcoming() {		
		return Mage::getStoreConfig('groupdeals/configuration/display_upcoming');
	}
	
	public function getTitle() {
		return $this->__('Upcoming deals');
	}
}