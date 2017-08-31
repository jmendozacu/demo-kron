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
class Simi_Ztheme_Model_Banner extends Simi_Ztheme_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('ztheme/banner');
    }

    public function getBanners($data, $phone_type) {
   
        $list = $this->getListBanner($data, $phone_type);
        
        if (count($list)) {
            $information = $this->statusSuccess();
            $information['data'] = $list;
            return $information;
        } else {
            $information = $this->statusError();
            return $information;
        }
    }
    
    public function getBannersAndSpot($data,$phone_type) {
        $bannerList = $this->getListBanner($data, $phone_type);
        $spotList = Mage::getModel('ztheme/spotproduct')->getSpotList($data,$phone_type);
        $bannerAndSpot = array_merge($bannerList, $spotList);
        
        if (count($bannerAndSpot)) {
            $information = $this->statusSuccess();
            $information['data'] = $bannerAndSpot;
            return $information;
        } else {
            $information = $this->statusError();
            return $information;
        }
    }

    public function getListBanner($data, $phone_type) {
        $website_id = Mage::app()->getStore()->getWebsiteId();
        $list = array();
        $collection = $this->getCollection()
                ->addFieldToFilter('status', 1)
                ->setOrder('banner_position', 'ASC')
                ->addFieldToFilter('website_id', array('in' => array($website_id, 0)));
                
        foreach ($collection as $item) {

            $categoryId = $item->getCategoryId();
            $cat = Mage::getModel('catalog/category')->load($categoryId);
            
            //child cats
            $childCats = array();
            foreach (explode(',', $cat->getChildren()) as $subCatid) {
                $_category = Mage::getModel('catalog/category')->load($subCatid);
                if ($_category->getIsActive()) {
                    $subCategory = array();
                    $subCategory['category_id'] = $subCatid;
                    $subCategory['category_name'] = $_category->getName();
                    if ($_category->hasChildren())
                        $subCategory['has_child'] = 'YES';
                    else
                        $subCategory['has_child'] = 'NO';
                    $childCats[] = $subCategory;
                }
            }

            //has child
            if ($cat->hasChildren())
                $hasChild = 'YES';
            else
                $hasChild = 'NO';
            
            //image
            $path = '';
            if (($item->getBannerName()) && ($item->getBannerName() != ''))
                $path = Mage::getBaseUrl('media') . 'simi/ztheme/banner' . '/' . $item->getWebsiteId() . '/' . $item->getBannerName();
            if (($phone_type == 'tablet') && ($item->getBannerNameTablet()) && ($item->getBannerNameTablet() != ''))
                $path = Mage::getBaseUrl('media') . 'simi/ztheme/banner_tab' . '/' . $item->getWebsiteId() . '/' . $item->getBannerNameTablet();
            
            //title
            $title = '';
            if (($this->getConfig("show_title") != 0) &&($item->getBannerTitle()))
                $title = $item->getBannerTitle();
                
            $list[] = array(
                'type' => 'cat',
                'category_image' => $path,
                'category_id' => $categoryId,
                'category_name' => $cat->getName(),
                'has_child' => $hasChild,
                'child_cat' => $childCats,
                'title' => $title,
            );
        }
        return $list;
    }
    
    

}
