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
class Simi_Themeone_Model_Spotproduct_Spot extends Simi_Themeone_Model_Spotproduct {
 protected function getHelper() {
        return Mage::helper('connector');
    }
     public function getOptionModel() {
        return Mage::getModel('connector/catalog_product_options');
    }
    public function getSpotProduct($data, $phone_type) {
        
//         if (Mage::getStoreConfig('themeone/general/enable') == 0) {
//            $information = $this->statusError(array('Extesnion was disabled'));
//            return $information;
//        }
        
        //process data here if need
        $spots = Mage::getModel('themeone/spotproduct')->getCollection()->setOrder('position', 'ASC')->addFieldToFilter('status', 1);
        $spotList = array();
        $key = Mage::getModel('themeone/config')->toKeySpotArray();
        $counter = 0;
        $phone_type_get=$phone_type;
        //$phone_type_get = "phone";
        foreach ($spots as $spot) {
            $spot_id = $spot->getData('spotproduct_id');
            $spot_name = $spot->getData('spotproduct_name');
            $spot_key = $spot->getData('spotproduct_key');
            //$spot_status=$spot->getData('status');
//            if ($phone_type == "tablet") {
//                if ($counter % 2 == 0)
//                    $phone_type_get = "tablet";
//                else
//                    $phone_type_get = "phone";
//                $counter++;
//            }

            $images = Mage::getModel('themeone/images')->getCollection()->addFieldToFilter('image_type', 'spotproduct')
                    ->addFieldToFilter('image_type_id', $spot_id)->addFieldToFilter('phone_type', $phone_type_get)->addFieldToFilter('image_delete', 2)
                    ->setOrder('status', 'DESC');
            $imageList = array();
            foreach ($images as $image) {
                $storeId = $image->getData('store_id');
                $image_type = $image->getData('image_type');
                $image_type_id = $image->getData('image_type_id');
                $option = $image->getData('options');
                $phone_type_get = $image->getData('phone_type');
                $image_name = $image->getData('image_name');

                $image_url = Mage::helper('themeone')->getImagePathForResponse($storeId, $image_type, $image_type_id, $option, $image_name, $phone_type_get);
                $imageList[] = $image_url;
            }

            $spotList[] = array(
                "spot_id" => $spot_id,
                "spot_name" => $spot_name,
                "spot_key" => $spot_key,
                "images" => $imageList,
            );
        }

        $information = $this->statusSuccess();
        $information['data'] = $spotList;
        return $information;
    }
    
    protected $_size=0;
    public $_sort = 0;
    public $_limit = 4;
    public $_offset = 0;
    public $_width = 600;
    public $_height = 600;
    public $_style="new_update";
    
    public function changeData($data_change, $event_name, $event_value) {
        $this->_data = $data_change;
        $this->eventChangeData($event_name, $event_value);
        return $this->getCacheData();
    }

    public function setCacheData($data, $module_name = '') {
        if ($module_name == "simi_connector") {
            $this->_data = $data;
            return;
        }

        $this->_data = $data;
    }

    public function getCacheData() {
        return $this->_data;
    }

    public function getImageProduct($product, $file = null) {
        if (!is_null($this->_width) && !is_null($this->_height)) {
            if ($file) {
                return Mage::helper('catalog/image')->init($product, 'thumbnail', $file)->constrainOnly(TRUE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize($this->_width, $this->_height)->__toString();
            }
            return Mage::helper('catalog/image')->init($product, 'small_image')->constrainOnly(TRUE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize($this->_width, $this->_height)->__toString();
        }
        if ($file) {
            return Mage::helper('catalog/image')->init($product, 'thumbnail', $file)->constrainOnly(TRUE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize(600, 600)->__toString();
        }
        return Mage::helper('catalog/image')->init($product, 'small_image')->constrainOnly(TRUE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize(600, 600)->__toString();
    }

    public function getSpotProducts($data) {
        if (Mage::getStoreConfig('themeone/general/enable') == 0) {
            $information = $this->statusError(array('Extesnion was disabled'));
            return $information;
        }
        $this->_sort=$data->sort_option;
       $this->_limit = $data->limit;
      $this->_offset = $data->offset;
      $this->_width = $data->width;
       $this->_height = $data->height;
       $this->_style = $data->key;

        $information = $this->statusSuccess();
        $productlist = array();
        if (!isset($this->_style) || !$this->_style || $this->_style == "none") {
            $information = $this->statusError(array('Key is Null'));
            return $information;
/*
            $style_best = $this->getSpotStatus('best_seller');
            $style_most = $this->getSpotStatus('most_view');
            $style_new = $this->getSpotStatus('new_update');
            $style_recent = $this->getSpotStatus('recent_add');
            $style_feature = $this->getSpotStatus('feature');


            if ($style_best == 1) {
                $productCollection = $this->getBetterProduct();
                $title = Mage::helper("core")->__("Best Seller");
                $key = "best_seller";
                $products = $this->getProductList($productCollection, $title, $key);
                $productlist = $products;
            }

            if ($style_most == 1) {
                $productCollection = $this->getMostviewProduct();
                $title = Mage::helper("core")->__("Most View");
                $key = "most_view";
                $products = $this->getProductList($productCollection, $title, $key);
                $productlist = $products;
            }

            if ($style_new == 1) {
                $productCollection = $this->getNewupdateProduct();
                $title = Mage::helper("core")->__("Newly Updated");
                $key = "new_update";
                $products = $this->getProductList($productCollection, $title, $key);
                $productlist = $products;
            }

            if ($style_recent == 1) {
                $productCollection = $this->getRecentlyAddProduct();
                $title = Mage::helper("core")->__("Recently Added");
                $key = "recent_add";
                $products = $this->getProductList($productCollection, $title, $key);
                $productlist = $products;
            }
            if ($style_feature == 1) {
                $productCollection = $this->getFeatureProduct();
                $title = Mage::helper("core")->__("Feature");
                $key = "feature";
                $products = $this->getProductList($productCollection, $title, $key);
                //  $productlist[] = $products;
                $productlist = $products;
            }
            $information["data"] = $productlist;*/
        } else {
            //$path = "spotproduct/general/" . $style;
            $style_best = $this->getSpotStatus($this->_style);
            if ($style_best == 1) {
                $productCollection = null;
                $title = "";
                if ($this->_style == "recent_add") {
                    $productCollection = $this->getRecentlyAddProduct();
                    $title = Mage::helper("core")->__("Recently Added");
                } elseif ($this->_style == "most_view") {
                    $productCollection = $this->getMostviewProduct();
                    $title = Mage::helper("core")->__("Most View");
                } elseif ($this->_style == "new_update") {
                 
                    $productCollection = $this->getNewupdateProduct();
                    $title = Mage::helper("core")->__("Newly Updated");
                } elseif ($this->_style == "best_seller") {
                    $productCollection = $this->getBetterProduct();
                    $title = Mage::helper("core")->__("Best Seller");
                } elseif ($this->_style == "feature") {
                    $productCollection = $this->getFeatureProduct();
                    $title = Mage::helper("core")->__("Feature");
                }


                $key = $this->_style;
                $products = $this->getProductList($productCollection, $title, $key);
                $productlist = $products;
            }
            $information['message'] = array($this->_size);//count($productlist)
            $information["data"] = $productlist;
            $producIdArray = array();
            foreach ($productCollection as $key => $product) {
                $producIdArray[] = $product->getEntityId();
            }
            $information['other'][0] = array('product_id_array' => $producIdArray);
        }
        return $information;
    }

    public function getSpotStatus($key) {
        $model = Mage::getModel('themeone/spotproduct')->getCollection()->addFieldToFilter('spotproduct_key', $key)
                ->getFirstItem();
        $status = $model->getData('status');
        if ($status == null)
            return 0;
        return $status;
    }

    public function getProductList($productCollection, $title, $key="") {

        $information = array();
        $productList = array();

        if ($productCollection && $productCollection->getSize()) {
                     $pagesize=Mage::getModel('themeone/spotproduct')->getCollection()
                             ->addFieldToFilter("spotproduct_key",$this->_style)->getFirstItem()->
                             getData("pagesize");
                     if(!isset($pagesize) || $pagesize ==null) $pagesize=30;
            $productCollection->addUrlRewrite(0);
            $productCollection->setPageSize($pagesize);
            
            $this->_size=$productCollection->getSize()>$pagesize?$pagesize:$productCollection->getSize();
            
             $check_limit = 0;
            $check_offset = 0;

//             if ($offset > $productCollection->getSize())
//            return $this->statusError(array('No information'));

            $newarray=array();
            $check_size=0;
            foreach ($productCollection as $product) {
              if($check_size++>=$this->_size) break;
                $newarray[]=$product;  
            }
            
            sortCustom($newarray,$this->_sort);
            $productCollection=$newarray;
            foreach ($productCollection as $product) {
                if (++$check_offset <= $this->_offset) {
                    continue;
                }
                if (++$check_limit > $this->_limit)
                    break;
                $_currentProduct = Mage::getModel('catalog/product')->load($product->getId());

            $ratings = Mage::getModel('connector/review')->getRatingStar($product->getId());
            $total_rating = $this->getHelper()->getTotalRate($ratings);
            $avg = $this->getHelper()->getAvgRate($ratings, $total_rating);
            $prices = $this->getOptionModel()->getPriceModel($product);
                
                
                $info_product = array(
                    'product_id' => $_currentProduct->getId(),
                    'product_name' => $_currentProduct->getName(),
                    'product_type' => $_currentProduct->getTypeId(),
                    'product_regular_price' => Mage::app()->getStore()->convertPrice($product->getPrice(), false),
                    'product_price' => Mage::app()->getStore()->convertPrice($product->getFinalPrice(), false),
                    'stock_status' => $_currentProduct->isSaleable(),
                    'product_rate' => $avg,
                    'product_review_number' => $ratings[5],
                    'product_image' => $this->getImageProduct($_currentProduct, null),
                    'manufacturer_name' => $_currentProduct->getAttributeText('manufacturer') == false ? '' : $_currentProduct->getAttributeText('manufacturer'),
                    'is_show_price' => true,
                ); 
                
                if ($prices) {
                $info_product = array_merge($info_product, $prices);
            }
                Mage::helper("connector/tax")->getProductTax($product, $info_product, true, false);

                $event_name = $this->getControllerName() . '_product_detail';
                $event_value = array(
                    'object' => $this,
                    'product' => $_currentProduct
                );
                $data_change = $this->changeData($info_product, $event_name, $event_value);
                $productList[] = $data_change;
            }
        }
        return $productList;
    }

    public function getDisableModule() {
        $information = $this->statusError(array('Extesnion was disabled'));
        return $information;
    }

    public function getBetterProduct() {        
        $_productCollection = Mage::getResourceModel('reports/product_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addOrderedQty()
                ->addMinimalPrice()
                ->addTaxPercents()
                ->addStoreFilter()
                ->setOrder('ordered_qty', 'desc');

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($_productCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($_productCollection);
        return $_productCollection;
    }

    public function getMostviewProduct() {

        $_productCollection = Mage::getResourceModel('reports/product_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
               ->addViewsCount()
                ->addMinimalPrice()
                ->addTaxPercents()
                ->addStoreFilter();

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($_productCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($_productCollection);
  
        return $_productCollection;
    }

    public function getNewupdateProduct() {
        
       
        
        $_productCollection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->setOrder('update_at', 'desc');


        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($_productCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($_productCollection);
        return $_productCollection;
    }

    public function getRecentlyAddProduct() {
        $_productCollection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->setOrder('created_at', 'desc');


        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($_productCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($_productCollection);
        return $_productCollection;
    }

    public function getFeatureProduct() {
        $_productCollection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addAttributeToFilter('fb_product', array('eq' => '1'))
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents();


        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($_productCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($_productCollection);
        return $_productCollection;
    }
}
function cmpNameASC($a, $b)
{
    return strcmp($a->getName(), $b->getName());
}
function cmpNameDesc($a, $b)
{
    return -strcmp($a->getName(), $b->getName());
}
function cmpPriceDESC($a, $b)
{
    $ap=$a->getPrice();
    $bp=$b->getPrice();
    $result=0;
    if($ap==$bp) $result=0;
    else
    $result=$ap>$bp?-1:1;
    return $result;
}
function cmpPriceASC($a, $b)
{
    $ap=$a->getPrice();
    $bp=$b->getPrice();
    $result=0;
    if($ap==$bp) $result=0;
    else
    $result=$ap>$bp?1:-1;
    return $result;
}

function sortCustom(&$array,$sort_option){
    switch ($sort_option) {
        case 0: return false;
        case 1: usort($array,"cmpPriceASC"); return true;
        case 2: usort($array,"cmpPriceDESC"); return true;
        case 3: usort($array,"cmpNameASC"); return true;
        case 4: usort($array,"cmpNameDESC"); return true;
        
    }
}
