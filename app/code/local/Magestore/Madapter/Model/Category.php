<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Magestore_Madapter_Model_Category extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
    }

    public function getListCategory($categoryCollection, $limit, $offset) {
        $cateList = array();        
        
        foreach ($categoryCollection as $cate) {		
//            if (!$cate->getIsActive())
//                continue;
//            if (++$check_offset <= $offset) {
//                continue;
//            }
//            if (++$check_limit > $limit)
//                break;			
			// Zend_debug::dump($cate->hasChildren());
            // $cateCache = $this->getCategories($cate->getId());
            if (!$cate->hasChildren()) {
                $cateList[] = array(
                    'category_id' => $cate->getId(),
                    'category_name' => $cate->getName() == null ? 'ROOT' : $cate->getName(),
                    'has_child' => 'NO',
                );
            } else {
                $cateList[] = array(
                    'category_id' => $cate->getId(),
                    'category_name' => $cate->getName() == null ? 'ROOT' : $cate->getName(),
                    'has_child' => 'YES',
                );
            }
        }
       
        $tmp = Array();
        foreach ($cateList as &$ma)
            $tmp[] = &$ma["category_name"];
        array_multisort($tmp, $cateList);

        return $cateList;
    }

    public function getCategories($category_id , $offset = null, $limit = null) {	
        $recursionLevel = max(0, (int) Mage::app()->getStore()->getConfig('catalog/navigation/max_depth'));
        $parent = Mage::app()->getStore()->getRootCategoryId();        
        if ($category_id) {
            $parent = $category_id;
        }
		$category = Mage::getModel('catalog/category')->load($parent);		
		//$child = $category->getResource()->getChildren($category, false);		
        $categoryCollection = $category->getChildrenCategories();
		//Zend_debug::dump($categoryCollection);die();
        if ($limit == null && $offset == NULL) {
            return $categoryCollection;
        }                 		        
        return $this->getListCategory($categoryCollection, $limit, $offset);
    }

}
