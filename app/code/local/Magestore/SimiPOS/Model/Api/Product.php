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
 * SimiPOS Product API Model
 * Use to call api with prefix: product
 * Methods:
 *  product.list
 *  product.info
 *  product.options
 *  product.image
 *  product.detail
 *  product.additional
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Product extends Magestore_SimiPOS_Model_Api_Abstract
{
    protected $_filtersMap = array(
        'product_id' => 'entity_id',
        'set'        => 'attribute_set_id',
        'type'       => 'type_id'
    );
    
    /**
     * Retrieve product list by filter
     * 
     * @param array|null $filters
     * @param int|null $page
     * @param int|null $limit
     * @param boolean $price
     * @return array
     */
    public function apiList($filters = null, $page = null, $limit = null, $price = false)
    {
        if (is_array($filters) && !empty($filters['category'])) {
            $category = Mage::getModel('catalog/category')
                ->setStoreId($this->getStoreId())
                ->load($filters['category']);
            if ($category->getId()) {
                $collection = $category->setStoreId($this->getStoreId())->getProductCollection();
                unset($filters['category']);
            } else {
                return array();
            }
        } else {
            $collection = Mage::getResourceModel('catalog/product_collection')->setStoreId($this->getStoreId());
        }
        $barcode = Mage::getStoreConfig('simipos/catalog/barcode', $this->getStoreId());
        $collection->addStoreFilter($this->getStoreId())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect($barcode)
            ->addAttributeToSelect('small_image')
            ->addAttributeToSort('name');
        if ($price) {
        	$collection->addFinalPrice();
        }
        $collection->getSelect()->joinLeft(array('fulltext' => $collection->getTable('catalogsearch/fulltext')),
            'e.entity_id = fulltext.product_id AND fulltext.store_id = ' . $this->getStoreId(),
            array('data_index' => 'fulltext.data_index')
        );
        
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
//         Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        
        if (is_array($filters)) {
            foreach ($filters as $field => $value) {
                if ($field == 'search') {
                    if (is_array($value)) {
                        $like = isset($value['like']) ? $value['like'] : '%';
                    } else {
                        $like = '%' . $value . '%';
                    }
                    $collection->getSelect()->where('fulltext.data_index LIKE ?', $like);
                    continue;
                }
                if (isset($this->_filtersMap[$field])) {
                    $field = $this->_filtersMap[$field];
                }
                $collection->addFieldToFilter($field, $value);
            }
        }
        if ($page > 0) {
            $collection->setCurPage($page);
        }
        if ($limit > 0) {
//             $collection->setPageSize($limit);
        }
        
        $result = array(
            'total' => $collection->getSize()
        );
        foreach ($collection as $product) {
            $productData = array(
                'sku'   => $product->getSku(),
                'name'  => $product->getName(),
                'image' => Mage::helper('catalog/image')->init($product, 'small_image')->resize(230)->__toString(),
                'has_options'   => Mage::getSingleton('simipos/api_product_options')->hasOptions($product),
                'data_index'    => $product->getDataIndex(),
                // 'price' => $price ? $product->getFinalPrice() : 0,
            );
            if ($product->getData($barcode)) {
            	$productData['barcode'] = substr($product->getData($barcode), 1);
            }
            $result[$product->getId()] = $productData;
        }
        
        return $result;
    }
    
    /**
     * Retrieve product information (reduced) by API
     * 
     * @param int|string $productId
     * @param string|null $identifierType
     * @return array
     */
    public function apiInfo($productId, $identifierType = null)
    {
        $product = $this->_getProduct($productId, $identifierType);
        
        return array(
            'id'    => $product->getId(),
            'sku'   => $product->getSku(),
            'name'  => $product->getName(),
            'image' => Mage::helper('catalog/image')->init($product, 'small_image')->resize(230)->__toString(),
            'has_options'   => Mage::getSingleton('simipos/api_product_options')->hasOptions($product),
            'options'   => Mage::getSingleton('simipos/api_product_options')->getOptions($product),
        );
    }
    
    /**
     * Retrieve product options by API
     * 
     * @param int|string $productId
     * @param string|null $identifierType
     * @return array
     */
    public function apiOptions($productId, $identifierType = null)
    {
        $product = $this->_getProduct($productId, $identifierType);
        return Mage::getSingleton('simipos/api_product_options')->getOptions($product);
    }
    
    /**
     * Return loaded product instance
     * 
     * @param int|string $productId (SKU or ID)
     * @param string|null $identifierType
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct($productId, $identifierType = null)
    {
        if (is_object($productId)) {
            return $productId;
        }
        $product = Mage::getModel('catalog/product');
        $product->setStoreId($this->getStoreId());
        
        if ($identifierType == 'sku') {
            $idBySku = $product->getIdBySku($productId);
            if ($idBySku) {
                $product->load($idBySku);
            }
        } else {
            $product->load($productId);
        }
        if (!$product->getId()) {
            throw new Exception($this->_helper->__('Product is not found.'), 21);
        }
        $product->getTypeInstance(true)->setStoreFilter($this->getStoreId(), $product);
        return $product;
    }
    
    /**
     * get all image for product
     * 
     * @param mixed $productId
     * @return array
     */
    public function apiImage($productId, $identifierType = null)
    {
        $product = $this->_getProduct($productId, $identifierType);
        
        $images = array();
        $media = $product->getData('media_gallery');
        if ($media && isset($media['images'])) {
            foreach ($media['images'] as $image) {
            	$images[] = array(
            	   'label' => $image['label'],
            	   'url'   => Mage::getBaseUrl('media').'catalog/product'.$image['file']
            	);
            }
        }
        return $images;
    }
    
    /**
     * Retrieve product information (full) by API
     * 
     * @param int|string $productId
     * @param string|null $identifierType
     * @return array
     */
    public function apiDetail($productId, $identifierType = null)
    {
    	$product = $this->_getProduct($productId, $identifierType);
    	
    	$detail = $this->apiInfo($product);
    	$detail['images'] = $this->apiImage($product);
    	$detail['additional'] = $this->apiAdditional($product);
    	
    	// Extra information
    	$detail['is_salable'] = $product->isSalable();
    	if ($product->getStockItem()->getManageStock()) {
    	   $detail['qty'] = $product->getStockItem()->getStockQty();
    	}
    	$detail['is_available'] = (bool)($detail['is_salable'] || Mage::getStoreConfig('simipos/general/ignore_checkout'));
    	$detail['short_description'] = $this->_helper->stripTags($product->getShortDescription());
    	$detail['description'] = $this->_helper->stripTags($product->getDescription());
    	$detail['price'] = $product->getPrice();
    	$detail['final_price'] = $product->getFinalPrice();
    	
    	return $detail;
    }
    
    /**
     * get product additional information
     * 
     * @param $productId
     * @param $identifierType
     * @return array
     */
    public function apiAdditional($productId, $identifierType = null)
    {
    	$product = $this->_getProduct($productId, $identifierType);
    	$attributes = $product->getAttributes();
    	$data = array();
    	foreach ($attributes as $attribute) {
    	   if ($attribute->getIsVisibleOnFront()) {
    	       $value = $attribute->getFrontend()->getValue($product);
    	       if (!$product->hasData($attribute->getAttributeCode())) {
                   $value = Mage::helper('catalog')->__('N/A');
               } elseif ((string)$value == '') {
                   $value = Mage::helper('catalog')->__('No');
               } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                   $value = $this->getStore()->convertPrice($value, true);
               }
               if (is_string($value) && strlen($value)) {
                   $data[] = array(
                       'label' => $attribute->getStoreLabel(),
                       'value' => $this->_helper->stripTags($value)
                   );
               }
    	   }
    	}
    	return $data;
    }
}
