<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * SimiPOS Checkout Product API Model
 * Use to call api with prefix: checkout_product
 * Methods:
 *  list
 *  add
 *  update
 *  qty
 *  price
 *  remove
 *  clear
 * 
 *  addCustom
 *  updateCustom
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Checkout_Product
    extends Magestore_SimiPOS_Model_Api_Checkout_Abstract
{
    /**
     * List product on current shopping cart
     * 
     * @return array
     */
    public function apiList()
    {
        $quote = $this->_getQuote();
        $result = array();
        if (!$quote->getItemsCount()) {
            return $result;
        }
        foreach ($quote->getAllItems() as $item) {
            $product = $item->getProduct();
            $result[$product->getId()] = array(
                'sku'   => $product->getSku(),
                'name'  => $product->getName(),
                'image' => Mage::helper('catalog/image')->init($product, 'small_image')->resize(230)->__toString(),
                'has_options'   => Mage::getSingleton('simipos/api_product_options')->hasOptions($product),
            );
        }
        return $result;
    }
    
    /**
     * Add product to cart and return shopping cart item
     * 
     * @param array $productData
     * @return array
     * @throws Exception
     */
    public function apiAdd($productData)
    {
        $quoteId = $this->_getSession()->getQuoteId();
        $quote = $this->_getQuote(true);
        
        $productData = $this->_prepareData($productData);
        if (isset($productData['id'])) {
            $productData['entity_id'] = $productData['id'];
        }
        
        if (isset($productData['entity_id'])) {
            $productByItem = Mage::helper('catalog/product')->getProduct(
                $productData['entity_id'], $this->getStoreId(), 'id'
            );
        } else if (isset($productData['sku'])) {
            $productByItem = Mage::helper('catalog/product')->getProduct(
                $productData['sku'], $this->getStoreId(), 'sku'
            );
        } else {
            throw new Exception($this->_helper->__('Product needs ID or SKU for loading.'), 51);
        }
        
        $productRequest = $this->_getProductRequest($productData);
        try {
            $result = $quote->addProduct($productByItem, $productRequest);
            if (is_string($result)) {
                throw new Exception($result, 52); // Cannot add product to cart
            }
            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->collectTotals()->save();
            if ($quoteId != $quote->getId()) {
                Mage::getSingleton('simipos/api_checkout_customer')->apiSet(array(
                    'mode'  => Mage_Checkout_Model_Type_Onepage::METHOD_GUEST
                ));
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 52); // Cannot add product to cart
        }
        if ($result->getParentItemId()) {
            $result = Mage::getModel('sales/quote_item')->load($result->getParentItemId());
        }
        $data = $this->_getItemData($result);
        $data['image'] = Mage::helper('catalog/image')
            ->init($productByItem, 'small_image')->resize(560, 440)->__toString();
        return $data;
    }
    
    public function apiAddBarcode($barcode)
    {
        $collection = Mage::getResourceModel('catalog/product_collection')->setStoreId($this->getStoreId());
        $barcodeAtt = Mage::getStoreConfig('simipos/catalog/barcode', $this->getStoreId());
        $collection->addStoreFilter($this->getStoreId())
            ->addFieldToFilter($barcodeAtt, '0'.$barcode);
        if (count($collection) < 1) {
        	throw new Exception($this->_helper->__('No product match with barcode "%s".', $barcode), 51);
        }
        $productByItem = Mage::helper('catalog/product')->getProduct(
            $collection->getFirstItem()->getId(), $this->getStoreId(), 'id'
        );
        
    	$quoteId = $this->_getSession()->getQuoteId();
        $quote = $this->_getQuote(true);
        
        $productRequest = $this->_getProductRequest(array('id' => $productByItem->getId()));
        try {
            $result = $quote->addProduct($productByItem, $productRequest);
            if (is_string($result)) {
                throw new Exception($result, 52); // Cannot add product to cart
            }
            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->collectTotals()->save();
            if ($quoteId != $quote->getId()) {
                Mage::getSingleton('simipos/api_checkout_customer')->apiSet(array(
                    'mode'  => Mage_Checkout_Model_Type_Onepage::METHOD_GUEST
                ));
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 52); // Cannot add product to cart
        }
        if ($result->getParentItemId()) {
            $result = Mage::getModel('sales/quote_item')->load($result->getParentItemId());
        }
        $data = $this->_getItemData($result);
        $data['image'] = Mage::helper('catalog/image')
            ->init($productByItem, 'small_image')->resize(560, 440)->__toString();
        $data['product_data'] = Mage::getModel('simipos/api_product')->apiInfo($productByItem);
        return $data;
    }
    
    /**
     * Update shopping cart item
     * 
     * @param int $itemId
     * @param array $productData
     * @return array
     */
    public function apiUpdate($itemId, $productData)
    {
        $quote = $this->_getQuote();
        
        $item = $quote->getItemById($itemId);
        if (!$item || !$item->getId()) {
            throw new Exception($this->_helper->__('Item is not found.'), 54);
        }
        if (empty($productData['qty'])) {
            $productData['qty'] = $item->getQty();
        }
        $customPrice = $item->getCustomPrice();
        if (!is_null($customPrice)) {
            $item->setCustomPrice(null);
            $item->setOriginalCustomPrice(null);
            $item->setRegularPrice(null);
        }
        $productRequest = $this->_getProductRequest($productData);
        try {
            $result = $quote->updateItem($itemId, $productRequest, null);
            if (is_string($result)) {
                throw new Exception($result, 52); // Cannot update quote item
            }
            $quote->collectTotals()->save();
            if (!is_null($customPrice)) {
                $quote->setTotalsCollectedFlag(false);
                return $this->apiPrice($itemId, $customPrice);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 55); // Cannot update quote item
        }
        if ($result->getParentItemId()) {
            $result = Mage::getModel('sales/quote_item')->load($result->getParentItemId());
        }
        return $this->_getItemData($result);
    }
    
    /**
     * Update Item Qty
     * 
     * @param type $itemId
     * @param type $qty
     * @return boolean
     * @throws Exception
     */
    public function apiQty($itemId, $qty)
    {
        $quote = $this->_getQuote();
        
        $item = $quote->getItemById($itemId);
        if (!$item || !$item->getId()) {
            throw new Exception($this->_helper->__('Item is not found.'), 54);
        }
        try {
            $item->setQty($qty);
            if ($item->getHasError()) {
                throw new Exception($item->getMessage(), 52);
            }
            $quote->collectTotals()->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 55); // Cannot update quote item
        }
        return $this->_getItemData($item);
    }
    
    /**
     * Update product item price
     * 
     * @param type $itemId
     * @param type $price
     * @return array
     * @throws Exception
     */
    public function apiPrice($itemId, $price = null)
    {
        $quote = $this->_getQuote();
        
        $item = $quote->getItemById($itemId);
        if (!$item || !$item->getId()) {
            throw new Exception($this->_helper->__('Item is not found.'), 54);
        }
        if ($price == null) {
            // Remove custom price
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->setRegularPrice($price);
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $child->setCustomPrice($price);
                    $child->setOriginalCustomPrice($price);
                    $child->setRegularPrice($price);
                }
            }
            try {
                $quote->collectTotals()->save();
            } catch (Exception $e) {
                throw new Exception($e->getMessage(), 55); // Cannot update quote item
            }
            return $this->_getItemData($item);
        }
        $price = Mage::app()->getLocale()->getNumber($price);
        $price = max($price, 0);
        try {
            $itemsPrice = $item->getPrice();
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                // Custom price for children
                $itemsPrice = 0;
                foreach ($item->getChildren() as $child) {
                    if (is_null($child->getRegularPrice())) {
                        $child->setRegularPrice($child->getPrice());
                    }
                    $itemsPrice += $child->getQty() * $child->getRegularPrice();
                }
                $rate = $price / $itemsPrice;
                foreach ($item->getChildren() as $child) {
                    $customPrice = $quote->getStore()->roundPrice(
                        $rate * $child->getQty() * $child->getRegularPrice()
                    );
                    $child->setCustomPrice($customPrice);
                    $child->setOriginalCustomPrice($customPrice);
                }
            }
            if (is_null($item->getRegularPrice())) {
                $item->setRegularPrice($itemsPrice);
            }
            $quote->collectTotals()->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 55); // Cannot update quote item
        }
        // $item = $quote->getItemById($itemId);
        return $this->_getItemData($item);
    }
    
    /**
     * Remove quote item
     * 
     * @param int $itemId
     * @return boolean
     * @throws Exception
     */
    public function apiRemove($itemId)
    {
        $quote = $this->_getQuote();
        try {
            $quote->removeItem($itemId);
            $quote->collectTotals()->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 53); // Cannot remove item
        }
        return true;
    }
    
    public function apiClear()
    {
        $quote = $this->_getQuote();
        try {
            foreach ($quote->getItemsCollection() as $item) {
                if ($item->getId()) {
                    $quote->removeItem($item->getId());
                }
            }
            $quote->collectTotals()->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 53); // Cannot remove item
        }
        return true;
    }
    
    /**
     * get product request (object)
     * 
     * @param mixed $requestInfo
     * @return Varien_Object
     */
    protected function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof Varien_Object) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new Varien_Object();
            $request->setQty($requestInfo);
        } else {
            $request = new Varien_Object($this->_helper->prepareData($requestInfo));
        }
        if (!$request->hasQty()) {
            $request->setQty(1);
        }
        return $request;
    }
    
    /**
     * Custom sales product
     * 
     * @param array $productData
     * @return array
     * @throws Exception
     */
    public function apiAddCustom($productData)
    {
        /**
         * $productData
         *  name
         *  price
         *  is_virtual
         */
        $product = $this->_helper->getCustomSaleProduct();
        if (!$product) {
            throw new Exception($this->_helper->__('Product needs ID or SKU for loading.'), 51);
        }
        $productData['id'] = $product->getId();
        $result = $this->apiAdd($productData);
        if (empty($result['price']) && isset($productData['price'])) {
        	$result['price'] = $productData['price'];
        }
        return $result;
    }
    
    public function apiUpdateCustom($itemId, $productData)
    {
        /**
         * $productData
         *  name
         *  price
         *  is_virtual
         */
        return $this->apiUpdate($itemId, $productData);
    }
    
    protected function _getItemData($item)
    {
    	if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
    		Mage::getSingleton('cataloginventory/observer')->getCleanObject();
    	}
    	foreach ($this->_getQuote()->getAllVisibleItems() as $_item) {
    		if ($_item->getId() == $item->getId()) {
    			return parent::_getItemData($_item);
    		}
    	}
    	return parent::_getItemData($item);
    }
}
