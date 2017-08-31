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
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Madapter Model
 * 
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @author  	Magestore Developer
 */
class Magestore_Madapter_Model_Product extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
    }

    public function getImageProduct($product, $file = null) {
        if ($file) {
            return Mage::helper('catalog/image')->init($product, 'thumbnail', $file)->__toString();
        }
        return Mage::helper('catalog/image')->init($product, 'small_image')->__toString();
    }

    public function getListProduct($productCollection, $offset, $limit, $sort_option) {
        $product_total = $productCollection->getSize();
        $sort = Mage::helper('madapter')->getSortOption($sort_option);

        if ($sort) {
            $productCollection->setOrder($sort[0], $sort[1]);
        }
        $productCollection->setPageSize($offset + $limit);
        $productList = array();
        $productList[] = $product_total;
        if ($offset > $product_total)
            return null;
        $check_limit = 0;
        $check_offset = 0;
        foreach ($productCollection as $product) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;
            $ratings = $this->getRatingStar($product->getId());
            $total_rating = $ratings[0] * 1 + $ratings[1] * 2 + $ratings[2] * 3 + $ratings[3] * 4 + $ratings[4] * 5;
            $avg = 0;
            if ($ratings[5] != 0)
                $avg = $total_rating / $ratings[5];
            // Mage::getModel('review/review')->getEntitySummary($product, $product->getStoreId());
            $productList[] = array(
                'product_id' => $product->getId(),
                'product_name' => $product->getName(),
                'product_price' => $product->getFinalPrice(),
                'product_rate' => $avg,
                'product_review_number' => $ratings[5],
                'product_image' => $this->getImageProduct($product),
                'manufacturer_name' => $product->getAttributeText('manufacturer') == false ? '' : $product->getAttributeText('manufacturer'),
            );
        }
        return $productList;
    }

    public function getProduct($id) {
        return Mage::getModel('catalog/product')->load($id);
    }

    public function getProductsCollection() {
        $productCollection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToSelect('*')
                ->addFinalPrice();

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($productCollection);
        $productCollection->addUrlRewrite(0);
        return $productCollection;
    }

    public function getProductsBestSeller($limit) {
        //$storeId = Mage::app()->getStore()->getId();
        $style_show = Mage::helper('madapter')->getConfig('spot_product_value');
        $productList = array();
        $productCollection = null;
        if ($style_show == 1) {
            $productCollection = Mage::getResourceModel('reports/product_collection')
                    ->addOrderedQty()
                    // ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes()) //edit to suit tastes                
                    ->setOrder('ordered_qty', 'desc'); //best sellers on top			
        } elseif ($style_show == 2) {
            $productCollection = Mage::getResourceModel('reports/product_collection')
                    ->addAttributeToSelect('*')
                    // ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())					
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents()
                    ->addViewsCount();
        } elseif ($style_show == 3) {
            $productCollection = Mage::getResourceModel('catalog/product_collection')
                    ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents()
                    ->setOrder('update_at', 'desc');
        } elseif ($style_show == 4) {
            $productCollection = Mage::getResourceModel('catalog/product_collection')
                    ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                    ->addMinimalPrice()
                    ->addFinalPrice()
                    ->addTaxPercents()
                    ->setOrder('created_at', 'desc');
        }


        if ($productCollection) {
            //echo $productCollection->getSelect()->__toString();die();
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productCollection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($productCollection);

            $productCollection->addUrlRewrite(0);
            $productCollection->setPageSize($limit);
            //Zend_debug::dump($productCollection->getData());die();
            $check_limit = 0;
            foreach ($productCollection as $_product) {
                if (++$check_limit > $limit)
                    break;
                $product = $this->getProduct($_product->getId());
                $productList[] = array(
                    'product_id' => $product->getId(),
                    'product_name' => $product->getName(),
                    'product_price' => $product->getFinalPrice(),
                    'product_image' => $this->getImageProduct($product),
                );
            }
        }
        return $productList;
    }

    public function getDetailProduct($id, $storeId = null) {
        $product = array();
        $_product = Mage::getModel('catalog/product')->load($id);

        $images = $_product->getMediaGallery();
        $image_url = array();
        foreach ($images['images'] as $image) {
            $image_url[] = $this->getImageProduct($_product, $image['file']);
        }
        if (count($image_url) == 0) {
            $image_url[] = $this->getImageProduct($_product);
        }
        $productId = $_product->getId();
        $storeId = $storeId == null ? Mage::app()->getStore()->getId() : $storeId;
        $star = $this->getRatingStar($productId);
        $option = array();
        if ($_product->isSaleable()) {
            $x = $this->getAllowAttributes($_product);
            if ($x[0] == 1) {
                $this->setOptionConfig($option, $x[1], $_product);
            } elseif ($x[0] == 2) {
                $this->setOptionsBundle($option, $x[1]);
            } elseif ($x[0] == 3) {
                $this->setOptionSimple($option, $x[1]);
            }
        }
        $total_rating = $star[0] * 1 + $star[1] * 2 + $star[2] * 3 + $star[3] * 4 + $star[4] * 5;
        $avg = 0;
        if ($star[5] != 0)
            $avg = $total_rating / $star[5];
        $product[] = array(
            'product_id' => $productId,
            'product_name' => $_product->getName(),
            'product_tyle' => $_product->getTypeId(),
            'product_regular_price' => $_product->getPrice(),
            'product_price' => $_product->getFinalPrice(),
            'product_feature' => $_product->getDescription(),
            'product_technical' => $_product->getInDepth(),
            'max_qty' => (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQty(),
            'store_id' => $storeId,
            'product_rate' => $avg,
            'product_images' => $image_url,
            'manufacturer_name' => $_product->getAttributeText('manufacturer') == false ? '' : $_product->getAttributeText('manufacturer'),
            'product_review_number' => $star[5],
            '5_star_number' => $star[4],
            '4_star_number' => $star[3],
            '3_star_number' => $star[2],
            '2_star_number' => $star[1],
            '1_star_number' => $star[0],
            'stock_status' => $_product->isSaleable(),
            'options' => $option,
        );

        return $product;
    }

    function getRatingStar($productId) {
        $reviews = Mage::getModel('review/review')
                ->getResourceCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addEntityFilter('product', $productId)
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                ->setDateOrder()
                ->addRateVotes();
        /**
         * Getting numbers ratings/reviews
         */
        $star = array();
        $star[0] = 0;
        $star[1] = 0;
        $star[2] = 0;
        $star[3] = 0;
        $star[4] = 0;
        $star[5] = 0;
        if (count($reviews) > 0) {
            foreach ($reviews->getItems() as $review) {
                $star[5]++;
                $y = 0;
                foreach ($review->getRatingVotes() as $vote) {
                    $y += ($vote->getPercent() / 20);
                }
                $x = (int) ($y / count($review->getRatingVotes()));
                $z = $y % 3;
                $x = $z < 5 ? $x : $x + 1;
                if ($x == 1) {
                    $star[0]++;
                } elseif ($x == 2) {
                    $star[1]++;
                } elseif ($x == 3) {
                    $star[2]++;
                } elseif ($x == 4) {
                    $star[3]++;
                } elseif ($x == 5) {
                    $star[4]++;
                } elseif ($x == 0) {
                    $star[5]--;
                }
            }
        }
        return $star;
    }

    public function hasOptions($product) {
        // Zend_debug::dump($product);die();
        if ($product->getTypeInstance(true)->hasOptions($product)) {
            return true;
        }
        return false;
    }

    public function getAllowProducts($_product) {
        if (!$this->hasAllowProducts()) {
            $products = array();
            $skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
            $allProducts = $_product->getTypeInstance(true)
                    ->getUsedProducts(null, $_product);
            foreach ($allProducts as $product) {
                if ($product->isSaleable() || $skipSaleableCheck) {
                    $products[] = $product;
                }
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }

    public function getAllowAttributes($product) {
        if ($product->getTypeId() == 'configurable') {
            return array(1, $product->getTypeInstance(true)->getConfigurableAttributes($product));
        } elseif ($product->getTypeId() == 'bundle') {
            return array(2, $this->getOptionsBundle($product));
        } elseif ($product->getTypeId() == 'simple') {
            return array(3, $product);
        } 
    }

    public function setOptionSimple(&$option, $product) {
        foreach ($product->getOptions() as $_option) {
            $type = null;
            if ($_option->getType() == 'multiple' || $_option->getType() == 'checkbox') {
                $type = 'multi';
            } elseif ($_option->getType() == 'drop_down' || $_option->getType() == 'radio') {
                $type = 'single';
            }
            /* @var $option Mage_Catalog_Model_Product_Option */
            $priceValue = 0;
            if ($_option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                $_tmpPriceValues = array();
                foreach ($_option->getValues() as $value) {
                    /* @var $value Mage_Catalog_Model_Product_Option_Value */
                    $_tmpPriceValues[$value->getId()] = Mage::helper('core')->currency($value->getPrice(true), false, false);
                    $option[] = array(
                        'option_id' => $value->getId(),
                        'option_value' => $value->getTitle(),
                        'option_price' => $value->getPrice(true),
                        'option_title' => $_option->getTitle(),
                        'position' => $_option->getSortOrder(),
                        'option_type_id' => $_option->getId(),
                        'option_select_type' => $type,
                        'is_required' => $_option->getIsRequire() == 1 ? 'YES' : 'No',
                    );
                }
                $priceValue = $_tmpPriceValues;
            } else {
                $priceValue = Mage::helper('core')->currency($option->getPrice(true), false, false);
            }
        }
    }

    public function setOptionConfig(&$option, $attributes, $_product) {
        $products = $this->getAllowProducts($_product);
        $list_value = array();
        foreach ($products as $product) {
            foreach ($attributes as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                //$productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                if (!in_array($attributeValue, $list_value))
                    $list_value[] = $attributeValue;
            }
        }

        foreach ($attributes as $attribute) {
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if (in_array($value['value_index'], $list_value))
                        $option[] = array(
                            'option_id' => $value['value_index'],
                            'option_value' => $value['label'],
                            'option_price' => $value['pricing_value'] != NULL ? $value['pricing_value'] : '0',
                            'option_title' => $attribute->getLabel(),
                            'position' => '0',
                            'option_type_id' => $attribute->getProductAttribute()->getId(),
                            'option_select_type' => 'single',
                            'is_required' => 'YES',
                        );
                }
            }
        }
    }

    public function getOptionsBundle($product) {
        $typeInstance = $product->getTypeInstance(true);
        $typeInstance->setStoreFilter($product->getStoreId(), $product);
        $optionCollection = $typeInstance->getOptionsCollection($product);

        $selectionCollection = $typeInstance->getSelectionsCollection(
                $typeInstance->getOptionsIds($product), $product
        );

        return $optionCollection->appendSelections($selectionCollection, false, false);
    }

    public function setOptionsBundle(&$option, $optionsArray) {
        foreach ($optionsArray as $_option) {
            $optiondId = $_option->getId();
            $title = $_option->getTitle();
            $position = $_option->getPosition();
            $type = $_option->getType();
            if ($type == 'multi' || $type == 'checkbox')
                $type = 'multi';
            else
                $type = 'single';
            $require = $_option->getRequired();
            foreach ($_option->getSelections() as $_selection) {
                $selectionId = $_selection->getSelectionId();
                $selectionName = $_selection->getName();
                $option[] = array(
                    'option_id' => $selectionId,
                    'option_value' => $selectionName,
                    'option_price' => $_selection->getPrice(),
                    'option_title' => $title,
                    'option_type_id' => $optiondId,
                    'option_select_type' => $type,
                    'position' => $position,
                    'is_required' => $require == 1 ? 'YES' : 'No',
                );
            }
        }
    }

    public function getRelateProducts($productId, $limit) {
        $productList = array();
        $_product = $this->getProduct($productId);
        $relateProducts = $_product->getRelatedProductCollection()->addAttributeToSelect('*')->addAttributeToSelect('required_options')
                ->addAttributeToSort('position', 'asc')
                ->addStoreFilter();
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($relateProducts);
        $relateProducts->setPageSize($limit);
        $relateProducts->load();
        $check_limit = 0;
        foreach ($relateProducts as $product) {
            if (++$check_limit > $limit)
                break;
            $productList[] = array(
                'product_id' => $product->getId(),
                'product_name' => $product->getName(),
                'product_price' => $product->getFinalPrice(),
                'product_image' => $this->getImageProduct($product),
            );
        }
        return $productList;
    }

    public function getCategoryProducts($categoryId, $limit, $offset, $storeId = null, $sort) {
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $storeId = $storeId == null ? Mage::app()->getStore()->getId() : $storeId;
        $productCollection = $category->getProductCollection()
                ->addAttributeToSelect('*')
                ->setStoreId($storeId)
                ->addFinalPrice();

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($productCollection);
        $productCollection->addUrlRewrite(0);
        //Zend_debug::dump($productCollection->getSelect()->__toString());die();
        return $this->getListProduct($productCollection, $offset, $limit, $sort);
    }

    public function getCartProducts($productIds) {
        $producList = array();
        $ids = explode("|", $productIds);
        $productCollection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('*')
                ->addFieldToFilter('entity_id', array('in' => $ids));
        foreach ($productCollection as $product) {
            $producList[] = array(
                'product_id' => $product->getId(),
                'product_name' => $product->getName(),
                'product_price' => $product->getFinalPrice(),
                'product_image' => $this->getImageProduct($product),
            );
        }
        return $producList;
    }

    public function getSearchProducts($keyword, $category_id, $sort_option, $offset, $limit) {
        $query = Mage::getModel('catalogsearch/query')
                ->loadByQuery($keyword);
        $productCollection = $query->getResultCollection()->addAttributeToSelect('*');

        if ($category_id) {
            $category = Mage::getModel('catalog/category')->load($category_id);
            $productCollection->addCategoryFilter($category);
        }

        $sort = Mage::helper('madapter')->getSortOption($sort_option);

        if ($sort) {
            $productCollection->setOrder($sort[0], $sort[1]);
        }

        return $this->getListProduct($productCollection, $offset, $limit, $sort_option);
    }

    public function getProductReview($productId, $limit, $offset, $storeId = null, $starProduct) {

        $storeId = $storeId == null ? Mage::app()->getStore()->getId() : $storeId;
        $reviews = Mage::getModel('review/review')
                ->getResourceCollection()
                ->addStoreFilter($storeId)
                ->addEntityFilter('product', $productId)
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                ->setDateOrder()
                ->addRateVotes();

        $list = array();
        $star = array();
        $count = null;
        $star[0] = 0;
        $star[1] = 0;
        $star[2] = 0;
        $star[3] = 0;
        $star[4] = 0;
        $star[5] = 0;

        if ($offset <= count($reviews) && count($reviews) > 0) {
            $check_limit = 0;
            $check_offset = 0;
            foreach ($reviews->getItems() as $review) {
                if (++$check_offset <= $offset) {
                    continue;
                }
                if (++$check_limit > $limit)
                    break;
                $star[5]++;
                $y = 0;
                foreach ($review->getRatingVotes() as $vote) {
                    $y += ($vote->getPercent() / 20);
                }
                $x = (int) ($y / count($review->getRatingVotes()));
                if (isset($starProduct) && $starProduct) {
                    if ($x == $starProduct) {
                        $list[] = array(
                            'review_id' => $review->getId(),
                            'customer_name' => $review->getNickname(),
                            'review_title' => $review->getTitle(),
                            'review_body' => $review->getDetail(),
                            'review_time' => $review->getCreatedAt(),
                            'rate_point' => $x,
                        );
                    }
                } else {
                    $list[] = array(
                        'review_id' => $review->getId(),
                        'customer_name' => $review->getNickname(),
                        'review_title' => $review->getTitle(),
                        'review_body' => $review->getDetail(),
                        'review_time' => $review->getCreatedAt(),
                        'rate_point' => $x,
                    );
                }
                $z = $y % 3;
                $x = $z < 5 ? $x : $x + 1;
                if ($x == 1) {
                    $star[0]++;
                } elseif ($x == 2) {
                    $star[1]++;
                } elseif ($x == 3) {
                    $star[2]++;
                } elseif ($x == 4) {
                    $star[3]++;
                } elseif ($x == 5) {
                    $star[4]++;
                } elseif ($x == 0) {
                    $star[5]--;
                }
            }
            $arr = array();
            $count = array(
                '1_star' => $star[0],
                '2_star' => $star[1],
                '3_star' => $star[2],
                '4_star' => $star[3],
                '5_star' => $star[4],
            );
            $array[] = $list;
            $array[] = $count;
            return Mage::helper('madapter')->endcodeReivewJson('reviewList', $list, $count);
        }
        return Mage::helper('madapter')->endcodeReivewJson('reviewList', array(), null);
    }

    public function getUsedProductOption(Mage_Catalog_Model_Product_Configuration_Item_Interface $item) {
        $typeId = $item->getProduct()->getTypeId();

        switch ($typeId) {
            case Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE:
                return $this->getConfigurableOptions($item);
                break;
            case Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE:
                return $this->getGroupedOptions($item);
                break;
        }
        return $this->getCustomOptions($item);
    }

    public function getConfigurableOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item) {
        $usedOptions = array();
        $product = $item->getProduct();
        //Zend_debug::dump(get_class_methods($product));die();
        $typeId = $product->getTypeId();
        if ($typeId != Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            Mage::throwException($this->__('Wrong product type to extract configurable options.'));
            return $usedOptions;
        }
        $attributes = $product->getCustomOption('attributes');
        //Zend_debug::dump($attributes);
        if ($attributes) {
            $data = unserialize($attributes->getValue());
            $productOptions = null;
            $this->setOptionConfig($productOptions, $product->getTypeInstance(true)->getConfigurableAttributes($product), $product);
            foreach ($data as $key => $value) {
                foreach ($productOptions as $option) {
                    if ($key == $option['option_type_id']
                            && $value == $option['option_id']) {
                        $usedOptions[] = $option;
                        break;
                    }
                }
            }
        }
        return $usedOptions;
    }

    public function getGroupedOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item) {
        return;
    }

    public function getCustomOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item) {
        $usedOptions = array();
        $product = $item->getProduct();
        $optionIds = $item->getOptionByCode('option_ids');
        if ($optionIds) {
            $productOptions = null;
            $this->setOptionSimple($productOptions, $product);
            $optionIds = explode(',', $optionIds->getValue());
            foreach ($optionIds as $optionId) {
                $option = $product->getOptionById($optionId);
                if ($option) {
                    $itemOption = $item->getOptionByCode('option_' . $option->getId());
                    foreach ($productOptions as $option_p) {
                        if ($option->getId() == $option_p['option_type_id']
                                && $itemOption->getValue() == $option_p['option_id']) {
                            $usedOptions[] = $option_p;
                            break;
                        }
                    }
                }
            }
        }
        return $usedOptions;
    }

    public function getBunldedOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item) {
        $product = $item->getProduct();
        $usedOptions = array();
        /**
         * @var Mage_Bundle_Model_Product_Type
         */
        $typeInstance = $product->getTypeInstance(true);

        // get bundle options
        $optionsQuoteItemOption = $item->getOptionByCode('bundle_option_ids');
        $bundleOptionsIds = $optionsQuoteItemOption ? unserialize($optionsQuoteItemOption->getValue()) : array();
        if ($bundleOptionsIds) {
            /**
             * @var Mage_Bundle_Model_Mysql4_Option_Collection
             */
            $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

            // get and add bundle selections collection
            $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

            $selectionsCollection = $typeInstance->getSelectionsByIds(
                    unserialize($selectionsQuoteItemOption->getValue()), $product
            );

            $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
            $this->setOptionsBundle($usedOptions, $bundleOptions);
            return $usedOptions;
        }
    }

    public function checkOption($data) {
        $productId = $data['product_id'];
        $optionId = $data['option_id'];
        $options = array();
        $product = Mage::getModel('catalog/product')->load($productId);
        if ($product->getTypeId() == 'configurable') {
            $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);

            $items = $this->getAllowProducts($product);
            $cacheItem = Mage::helper('madapter')->checkOptions($items, $optionId, $attributes);
            $cacheAttributes = array();
            foreach ($attributes as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $code = $productAttribute->getAttributeCode();
                $cacheAttributes[$code] = $cacheItem->getData($code);
            }

            foreach ($items as $item) {
                foreach ($cacheAttributes as $code => $value) {
                    $attributeValue = $item->getData($code);
                    if (in_array($attributeValue, $cacheAttributes)) {
                        Mage::helper('madapter')->setOptions($options, $item, $cacheAttributes);
                    }
                }
            }

            $options = array_unique($options);
        }
        $list = array();
        foreach ($options as $option) {
            if ($option != $optionId)
                $list[] = $option;
        }
        return $list;
    }

}