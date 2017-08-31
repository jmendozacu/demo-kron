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
 * @package     Magestore_ThemeOne
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Themeone Model
 * 
 * @category    Magestore
 * @package     Magestore_ThemeOne
 * @author      Magestore Developer
 */
class Simi_Themeone_Model_Categories_Categories extends Simi_Themeone_Model_Categories {

    public function getCategories($data,$phone_type) {
        
//         if (Mage::getStoreConfig('themeone/general/enable') == 0) {
//            $information = $this->statusError(array('Extesnion was disabled'));
//            return $information;
//        }
        
        //process data here if need
        $categories = Mage::getModel('themeone/categories')->getCollection()->setOrder('priority', 'ASC');
        $cateList = array();
        foreach ($categories as $category) {
            $priority = $category->getData('priority');
            $category_id = $category->getData('category_id');
            $category_name = $category->getData('category_name');
            $images = Mage::getModel('themeone/images')->getCollection()->addFieldToFilter('image_type', 'category')
                    ->addFieldToFilter('image_type_id', $priority)->addFieldToFilter('image_delete', 2)
                    ->setOrder('status', 'DESC');
            $imageList = array();
            foreach ($images as $image) {
                $storeId = $image->getData('store_id');
                $image_type = $image->getData('image_type');
                $image_type_id = $image->getData('image_type_id');
                $option = $image->getData('options');
                $image_name = $image->getData('image_name');
                 $phone_type_get= $image->getData('phone_type');

                $image_url = Mage::helper('themeone')->getImagePathForResponse($storeId, $image_type, $image_type_id, $option, $image_name,$phone_type_get);
                $imageList[] = $image_url;
            }
            if ($category_id >= 0) {
                $category = Mage::getModel('catalog/category')->load($category_id);
                if ($category->hasChildren()) {
                    $cateList[] = array(
                        "category_id" => $category_id,
                        "category_name" => $category_name,
                        'has_child' => 'YES',
                        "images" => $imageList,
                    );
                } else {
                    $cateList[] = array(
                        "category_id" => $category_id,
                        "category_name" => $category_name,
                        'has_child' => 'NO',
                        "images" => $imageList,
                    );
                }
            } else {
                $cateList[] = array(
                    "category_id" => $category_id,
                    "category_name" => $category_name,
                    'has_child' => 'YES',
                    "images" => $imageList,
                );
            }
        }
        if($phone_type=="tablet"){
            $cateList2=array();
            $cateList2[]=$cateList[1];
            $cateList2[]=$cateList[2];
            $cateList2[]=$cateList[0];
            $cateList2[]=$cateList[3];
            $cateList=$cateList2;
        }
        $information = $this->statusSuccess();
        $information['data'] = $cateList;
        return $information;
    }
}