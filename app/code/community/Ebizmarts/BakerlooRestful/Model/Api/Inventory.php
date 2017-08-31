<?php

class Ebizmarts_BakerlooRestful_Model_Api_Inventory extends Ebizmarts_BakerlooRestful_Model_Api_Api {

	protected $_model = 'catalog/product';

    public function getPageSize() {
        return parent::getSafePageSize();
    }

    /**
     * Use since from external table instead of catalog_product table.
     */
    public function _beforePaginateCollection($collection, $page, $since = null) {

        //Filter result by Website if StoreId is provided.
        $websiteId = Mage::app()->getStore($this->getStoreId())->getWebsiteId();
        if($websiteId) {
            $this->_collection->addWebsiteFilter($websiteId);
        }

        if(!$since or (-1 == $since) ) {
            return $this;
        }

        $this->_collection->getSelect()->joinLeft(
                            array('deltas' => Mage::getSingleton('core/resource')->getTableName('bakerloo_restful/inventorydelta')),
                            'e.entity_id = deltas.product_id',
                            array()
                            )
                          ->reset(Zend_Db_Select::WHERE)
                          ->where('deltas.updated_at >= ?', $since);
        return $this;
    }

    public function _createDataObject($id = null, $data = null) {

        $result = new Varien_Object;

        $product = Mage::getModel($this->_model)->setStoreId($this->getStoreId())->load($id);

        if($product->getId()) {
			$stockData = clone $product->getStockItem();

            $delta = Mage::getModel('bakerloo_restful/inventorydelta')->loadByProductId($id);

            if(!$delta->getId()) {
                $delta
                ->setInventoryItemId($stockData->getItemId())
                ->setProductId($id)
                ->save();
            }

            $stockData->setCreatedAt($delta->getCreatedAt());
            $stockData->setUpdatedAt($delta->getCreatedAt());

            if( ((int)Mage::helper('bakerloo_restful')->config('catalog/allow_backorders')) ) {
                $stockData->setBackorders(1);
                if( !Mage::registry(Ebizmarts_BakerlooRestful_Model_Rewrite_CatalogInventory_Stock_Item::BACKORDERS_YES) )
                    Mage::register(Ebizmarts_BakerlooRestful_Model_Rewrite_CatalogInventory_Stock_Item::BACKORDERS_YES, true);
            }

            Mage::dispatchEvent("pos_get_inventory", array("product" => $product, "stock_item" => $stockData));

            $result = array(
                'backorders'              => (int)$stockData->getBackorders(),
                'enable_qty_increments'   => (int)$stockData->getEnableQtyIncrements(),
                'is_qty_decimal'          => (int)$stockData->getIsQtyDecimal(),
                'is_in_stock'             => (int)$stockData->getIsInStock(),
                'manage_stock'            => (int)$stockData->getManageStock(),
                'manage_stock_use_config' => (int)$stockData->getUseConfigManageStock(),
                'product_id'              => (int)$stockData->getProductId(),
                'qty'                     => (is_null($stockData->getQty()) ? 0.0000 : $stockData->getQty()),
                'qty_increments'          => ($stockData->getQtyIncrements() === false ? 0.0000 : $stockData->getQtyIncrements()),
                'store_id'                => $stockData->getStoreId(),
                'updated_at'              => $stockData->getUpdatedAt(),
            );

        }

        return $result;
    }

    /**
     * Retrieve inventory data for a given array of product ids.
     *
     */
    public function multiple() {
        $ids = explode(",", $this->_getQueryParameter('products'));

        $result = array();

        if(is_array($ids) && !empty($ids)) {
            for($i = 0; $i < count($ids); $i++) {
                $data = $this->_createDataObject($ids[$i]);

                if(is_array($data) && !empty($data)) {
                    $result []= $data;
                }

            }
        }

        return $result;
    }

    /**
     * Update inventory for a given product.
     *
     * @return array
     */
    public function put() {
        parent::put();

        if(!$this->getStoreId()) {
            Mage::throwException('Please provide a Store ID.');
        }

        Mage::app()->setCurrentStore($this->getStoreId());

        $data = $this->getJsonPayload();

        $productId = (isset($data->product_id) ? ((int)$data->product_id) : null);

        $product = Mage::getModel($this->_model)->setStoreId($this->getStoreId())->load($productId);

        $oldData = clone $product->getStockItem();

        if($product->getId()) {
            $product->getStockItem()
            ->setQty($data->qty)
            ->setIsInStock($data->is_in_stock)
            ->setManageStock($data->manage_stock)
            ->save();

            Mage::dispatchEvent("pos_update_inventory", array("product" => $product, "old_stock_item" => $oldData, "new_stock_item" => $product->getStockItem()));

        }
        else {
            Mage::throwException('Product does not exist.');
        }

        return $this->_createDataObject($product->getId());
    }

}