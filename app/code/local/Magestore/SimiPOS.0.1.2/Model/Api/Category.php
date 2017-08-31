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
 * SimiPOS Category API Model
 * Use to call api with prefix: category
 * Methods:
 *  category.tree
 *  category.products
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Category extends Magestore_SimiPOS_Model_Api_Abstract
{
    /**
     * Convert tree node to array
     * 
     * @param Varien_Data_Tree_Node $node
     * @return array
     */
    protected function _nodeToArray(Varien_Data_Tree_Node $node)
    {
        $result = array();
        $result['name'] = $node->getName();
        $result['level'] = $node->getLevel();
        
        foreach ($node->getChildren() as $child) {
            if ($child->getIsActive()) {
                $result[$child->getId()] = $this->_nodeToArray($child);
            }
        }
        return $result;
    }
    
    /**
     * API liss all sub categories for root category by tree structure
     * 
     * @param type $parentId
     * @return array
     */
    public function apiTree($parentId = null)
    {
        $store = $this->getStore();
        if (is_null($parentId)) {
            $parentId = $store->getRootCategoryId();
        }
        
        /* @var $tree Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree */
        $tree = Mage::getResourceSingleton('catalog/category_tree')->load();
        
        $root = $tree->getNodeById($parentId);
        if($root && $root->getId() == 1) {
            $root->setName(Mage::helper('catalog')->__('Root'));
        }
        $collection = Mage::getResourceModel('catalog/category_collection')
            ->setStoreId($store->getId())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');
        
        $tree->addCollectionData($collection, true);
        
        $result = $this->_nodeToArray($root);
        $result['root'] = $root->getId();
        return $result;
    }
    
    /**
     * API list all product of a category
     * 
     * @param type $categoryId
     * @return array
     */
    public function apiProducts($categoryId)
    {
        $category = Mage::getModel('catalog/category')
            ->setStoreId($this->getStoreId())
            ->load($categoryId);
        if (!$category->getId()) {
            throw new Exception($this->_helper->__('Category does not exist.'), 22);
        }
        
        $collection = $category->setStoreId($this->getStoreId())->getProductCollection();
        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('small_image')
            ->addAttributeToSort('name');
        
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        
        $result = array(
            'total' => $collection->getSize()
        );
        foreach ($collection as $product) {
            $result[$product->getId()] = array(
                'sku'   => $product->getSku(),
                'name'  => $product->getName(),
                'image' => Mage::helper('catalog/image')->init($product, 'small_image')->resize(230)->__toString(),
                'has_options'   => Mage::getSingleton('simipos/api_product_options')->hasOptions($product),
            );
        }
        
        return $result;
    }
}
