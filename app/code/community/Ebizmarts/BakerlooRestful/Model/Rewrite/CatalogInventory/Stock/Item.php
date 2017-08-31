<?php

class Ebizmarts_BakerlooRestful_Model_Rewrite_CatalogInventory_Stock_Item extends Mage_CatalogInventory_Model_Stock_Item
{

	const BACKORDERS_YES = 'Ebizmarts_BakerlooRestful_Model_Rewrite_CatalogInventory_Stock_Item';

    /**
     * Retrieve backorders status
     *
     * @return int
     */
    public function getBackorders()
    {

    	if(Mage::registry(Ebizmarts_BakerlooRestful_Model_Rewrite_CatalogInventory_Stock_Item::BACKORDERS_YES))
    		return Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY;


    	return parent::getBackorders();

   }

}