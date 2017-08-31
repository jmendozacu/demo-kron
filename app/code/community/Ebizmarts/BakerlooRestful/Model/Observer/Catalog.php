<?php

class Ebizmarts_BakerlooRestful_Model_Observer_Catalog {

	const WEBSITES_REG_KEY = "bakerloorestful_product_original_websites";

	/**
	 * Save data to custom table when product is deleted from Magento.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Ebizmarts_BakerlooRestful_Model_Observer_Catalog
	 */
	public function productDelete(Varien_Event_Observer $observer) {
		$product = $observer->getEvent()->getProduct();

		if($product->getId()) {

			$storeId = Mage::app()->getRequest()->getParam('store');

			$trash = Mage::getModel('bakerloo_restful/catalogtrash');
			$trash
			->setProductId($product->getId())
			->setStoreId($storeId)
			->setAction('delete')
			->setProductSku($product->getSku())
			->setProductName($product->getName());

			$trash->save();
		}

		return $this;
	}


	/**
	 * Process product save, compare assigned websites, if something changed, save to table.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Ebizmarts_BakerlooRestful_Model_Observer_Catalog
	 */
	public function productSave(Varien_Event_Observer $observer) {
		$product = $observer->getEvent()->getProduct();

		$originalWebsiteIds = Mage::registry(self::WEBSITES_REG_KEY);
		$websiteIds         = $product->getWebsiteIds();

        if(!is_array($originalWebsiteIds)) {
            $originalWebsiteIds = array();
        }

		//Search for remove products form websites
		$deleted = array_diff($originalWebsiteIds, $websiteIds);

		//If removed from any Website, save to local DB for replication
		if(is_array($deleted) && !empty($deleted)) {

			foreach($deleted as $websiteId) {
                $websiteStores = Mage::app()->getWebsite($websiteId)->getStoreIds();

                if(is_array($websiteStores) && !empty($websiteStores)) {

                	foreach($websiteStores as $_stid) {
						$trash = Mage::getModel('bakerloo_restful/catalogtrash');
						$trash
						->setProductId($product->getId())
						->setStoreId($_stid)
						->setAction('remove_website')
						->setProductSku($product->getSku())
						->setProductName($product->getName());

						$trash->save();
                	}

                }

			}

		}

		return $this;
	}

	/**
	 * Process product PRE save.
	 *
	 * @param  Varien_Event_Observer $observer
	 * @return Ebizmarts_BakerlooRestful_Model_Observer_Catalog
	 */
	public function productPreSave(Varien_Event_Observer $observer) {
		$product = $observer->getEvent()->getProduct();
		$request = $observer->getEvent()->getRequest();


        $_product = Mage::getModel('catalog/product')
            		->setStoreId($request->getParam('store', 0))
            		->load($product->getId());

        //Save DB websites on local object to compare afterwards
		if(Mage::registry(self::WEBSITES_REG_KEY)) {
			Mage::unregister(self::WEBSITES_REG_KEY);
		}
		Mage::register(self::WEBSITES_REG_KEY, $_product->getWebsiteIds());

		return $this;
	}

	/**
	 * Monitor inventory when new order is placed.
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Ebizmarts_BakerlooRestful_Model_Observer_Catalog
	 */
	public function inventoryNewOrder(Varien_Event_Observer $observer) {

        $order = $observer->getEvent()->getOrder();
        $items = $order->getAllItems();

        foreach($items as $item) {

            if( ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) or ($item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) ) {
                continue;
            }

            $this->_saveInventoryChange($item->getProductId(), $item->getProduct()->getStockItem()->getItemId());

        }

        return $this;

	}

    /**
     * Monitor inventory when order is refunded.
     *
     * @param Varien_Event_Observer $observer
     * @return Ebizmarts_BakerlooRestful_Model_Observer_Catalog
     */
    public function inventoryNewCreditMemo(Varien_Event_Observer $observer) {
        $creditMemo = $observer->getEvent()->getCreditmemo();

        $items = $creditMemo->getAllItems();
        foreach($items as $item) {

            if($item->getQty()==0 or !$item->getBackToStock() ) {
                continue;
            }

            $_product = Mage::getModel('catalog/product')->load($item->getProductId());

			$this->_saveInventoryChange($item->getProductId(), $_product->getStockItem()->getItemId());
        }

        return $this;
    }

    /**
     * Monitor inventory when order item is canceled.
     *
     * @param Varien_Event_Observer $observer
     * @return Ebizmarts_BakerlooRestful_Model_Observer_Catalog
     */
    public function inventoryOrderItemCancel(Varien_Event_Observer $observer) {
        $item = $observer->getEvent()->getItem();

        $_product = Mage::getModel('catalog/product')->load($item->getProductId());

		$this->_saveInventoryChange($item->getProductId(), $_product->getStockItem()->getItemId());

		return $this;
    }

    /**
     * Save inventory change to database so we are able to use deltas on inventory sync.
	 *
     */
	private function _saveInventoryChange($productId, $stockItemId) {
		$delta = Mage::getModel('bakerloo_restful/inventorydelta')->loadByProductId($productId);
		$delta
			->setInventoryItemId($stockItemId)
			->setProductId($productId)
			->save();
	}

}