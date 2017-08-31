<?php

class Ebizmarts_BakerlooRestful_Model_V1_Inventory extends Ebizmarts_BakerlooRestful_Model_V1_Api {

	protected $_model = 'catalog/product';

    public function _createDataObject($id = null, $data = null) {

        $result = new Varien_Object;

        $product = Mage::getModel($this->_model)->setStoreId($this->getStoreId())->load($id);

        if($product->getId()) {
			$stockData = clone $product->getStockItem();

			Mage::dispatchEvent("pos_get_inventory", array("product" => $product, "stock_item" => $stockData));

			$result = $stockData->toArray();
        }

        return $result;
    }

}