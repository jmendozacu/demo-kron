<?php

class Ebizmarts_BakerlooRestful_Model_V1_Categories extends Ebizmarts_BakerlooRestful_Model_V1_Api {

    protected $_model = "catalog/category";
    protected $_count = 0;

    /**
     * Process GET requests.
     *
     * @return type
     * @throws Exception
     */
    public function get() {

        if($this->getFilesMode()) {
            return parent::get();
        }

        $identifier = $this->_getIdentifier();

        if($identifier) { //get item by id

            if(is_numeric($identifier)) {
                return $this->_createDataObject((int)$identifier);
            }
            else {
                throw new Exception('Incorrect request');
            }

        }
        else {

            return $this->_getCollectionPageObject(array($this->_getCategoryTree()), 1, null, null, $this->_count);
        }

    }

    public function _createDataObject($id = null, $data = null) {

        $result = array();

        if(is_null($data)) {
            $category = Mage::getModel($this->_model)->load($id);
        }
        else {
            $category = $data;
        }

        if($category->getId()) {

            $result = $this->_getCategoryTree($category->getId());

        }

        return $result;

    }

    /**
     * Retrieve category tree
     *
     * @param int $parentId
     * @param string|int $store
     * @return array
     */
    private function _getCategoryTree($parentId = null, $store = null) {
        if (is_null($parentId) && !is_null($store)) {
            $parentId = Mage::app()->getStore(0)->getRootCategoryId();
        } elseif (is_null($parentId)) {
            $parentId = 1;
        }

        /* @var $tree Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree */
        $tree = Mage::getResourceSingleton('catalog/category_tree')
            ->load();

        $root = $tree->getNodeById($parentId);

        if($root && $root->getId() == 1) {
            $root->setName(Mage::helper('catalog')->__('Root'));
        }

        $collection = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($this->getStoreId())
            //->addAttributeToFilter('is_active', 1)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_anchor')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('thumbnail')
            ->addAttributeToSelect('is_active');

        $this->_count = $collection->getSize();

        $tree->addCollectionData($collection, true);

        return $this->_nodeToArray($root);
    }

    /**
     * Convert node to array
     *
     * @param Varien_Data_Tree_Node $node
     * @return array
     */
    private function _nodeToArray(Varien_Data_Tree_Node $node) {

        // Only basic category data
        $result = array();
        $result['category_id'] = (int)$node->getId();
        $result['parent_id']   = (int)$node->getParentId();
        $result['name']        = (string)$node->getName();
        $result['is_active']   = (int)$node->getIsActive();

        $_image                = $node->getThumbnail() ? $node->getThumbnail() : $node->getImage();
        $result['image']       = $this->_getImageURL($node->getId(), (string)$_image);

        $result['position']    = (int)$node->getPosition();
        $result['is_anchor']   = (int)$node->getIsAnchor();
        $result['children']    = array();

        foreach ($node->getChildren() as $child) {
            $result['children'][] = $this->_nodeToArray($child);
        }

        return $result;
    }

    /**
     * Return category image url.
     *
     * @param      $categoryId
     * @param null $image
     *
     * @return string
     */
    private function _getImageURL($categoryId, $image = null) {

        $url = "";

        if($image) {
            $url = Mage::helper('bakerloo_restful')
                    ->getResizedImageUrl(null, $this->getStoreId(), $image, 150, 150, $categoryId);
        }

        return $url;
    }

}