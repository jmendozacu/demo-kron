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
 * @package     Magestore_Ztheme
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Ztheme Model
 * 
 * @category    Magestore
 * @package     Magestore_Ztheme
 * @author      Magestore Developer
 */
class Simi_Ztheme_Model_Category extends Simi_Ztheme_Model_Abstract {

    public $_categoryLevels;

    public function _construct() {
        parent::_construct();
        $this->_init('ztheme/category');
    }

    public function getCategoryTree($data) {
        $rootCatId = Mage::app()->getStore()->getRootCategoryId();
        if ($data->levels)
            $this->_categoryLevels = $data->levels;
        else
            $this->_categoryLevels = 2;
        $result = $this->getChildCats($rootCatId);
        $information = $this->statusSuccess();
        $information['data'] = $result;
        return $information;
    }

    public function getChildCats($parentId, $currentLevel = 0) {
        $allCats = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('is_active', '1')
                ->addAttributeToFilter('include_in_menu', '1')
                ->addAttributeToFilter('parent_id', array('eq' => $parentId))
                ->addAttributeToSort('position', 'asc');
        if (($allCats->count() == 0 ) || ($currentLevel >= $this->_categoryLevels ))
            return;
        $resultArray = array();
        foreach ($allCats as $category) {
            $childArray = array();
            $childArray['category_name'] = $category->getName();
            $childArray['category_id'] = $category->getEntityId();
            if (!$category->hasChildren())
                $childArray['has_child'] = 'NO';
            else
                $childArray['has_child'] = 'YES';
            $childArray['category_childs'] = $this->getChildCats($category->getEntityId(), $currentLevel + 1);
            $resultArray[] = $childArray;
        }
        return $resultArray;
    }

}
