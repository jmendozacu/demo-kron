<?php

	class Webkul_Preorder_Model_Stock extends Mage_CatalogInventory_Model_Stock {

		public function registerProductsSale($items) {
			$helper = Mage::helper("preorder");
			$qtys = $this->_prepareProductQtys($items);
			$item = Mage::getModel('cataloginventory/stock_item');
			$this->_getResource()->beginTransaction();
			$stockInfo = $this->_getResource()->getProductsStock($this, array_keys($qtys), true);
			$fullSaveItems = array();

			foreach ($stockInfo as $itemInfo) {
				// $productId = $item->getProductId();
				$productId = $itemInfo['product_id'];
				$item->setData($itemInfo);
				
				if(!$helper->isPreorder($productId)) {
					if (!$item->checkQty($qtys[$productId])) {
						$this->_getResource()->commit();
						Mage::throwException(Mage::helper('cataloginventory')->__('Not all products are available in the requested quantity'));
					}
					$item->subtractQty($qtys[$item->getProductId()]);
					if (!$item->verifyStock() || $item->verifyNotification()) {
						$fullSaveItems[] = clone $item;
					}
				}
			}
			$this->_getResource()->correctItemsQty($this, $qtys, '-');
			$this->_getResource()->commit();
			return $fullSaveItems;
		}
	}