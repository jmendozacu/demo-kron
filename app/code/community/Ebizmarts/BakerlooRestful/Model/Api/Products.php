<?php

class Ebizmarts_BakerlooRestful_Model_Api_Products extends Ebizmarts_BakerlooRestful_Model_Api_Api {

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'pos_api_product';

    protected $_model = "catalog/product";

    const IMAGES_CONF_PATH = 'default/bakerloorestful/product/imagesizes';

    public function getPageSize() {
        return parent::getSafePageSize();
    }

    /**
     * Process GET requests.
     *
     * @return array
     * @throws Exception
     */
    public function get() {

        if(!$this->getStoreId()) {
            Mage::throwException('Please provide a Store ID.');
        }

        return parent::get();

    }

    /**
     * Use since from external table instead of catalog_product table.
     */
    public function _beforePaginateCollection($collection, $page, $since = null) {

        if("catalog/product" == $this->_model) {
            return parent::_beforePaginateCollection($collection, $page, $since);
        }

        $this->_collection->addFieldToFilter('store_id',
                array(
                    array('eq'   => $this->getStoreId()),
                    array('null' => true),
                ));

        return $this;
    }

    protected function _getCollection() {

        if("catalog/product" != $this->_model) {
            return parent::_getCollection();
        }

        $collection = Mage::getModel($this->_model)
                ->getCollection()
                ->addAttributeToSelect('*')
                ->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner')
                ->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');

        $store = $this->getStore();
        if($store->getId()) {
            $collection->addStoreFilter($store);
        }

        return $collection;
    }

    public function _createDataObject($id = null, $data = null) {

        if(is_object($data) and ($data instanceof Ebizmarts_BakerlooRestful_Model_Catalogtrash)) {
            return $data->getData();
        }

        $result  = array();

        $product = Mage::getModel($this->_model)->setStoreId($this->getStoreId())->load($id);

        if($product->getId()) {

            //If data is null, no need to go to DB to fetch images
            if(is_null($data)) {
                $gallery = $product->getMediaGalleryImages();
            }
            else {
                $gallery = Mage::getModel('catalog/product')
                            ->setStoreId($this->getStoreId())
                            ->load($product->getId())
                            ->getMediaGalleryImages();
            }

            //Main image, some customers only have one image and use it as "exclude"
            if (($product->getImage() != 'no_selection') and $product->getImage()) {
                $mainImage = array(
                                   'file'     => $product->getImage(),
                                   'position' => 0,//@ToDo
                                   'label'    => $product->getData('image_label'),
                                   'url'      => Mage::getSingleton('catalog/product_media_config')->getMediaUrl($product->getImage()),
                );

                $gallery->addItem(new Varien_Object($mainImage));
            }

            //Images
            $galleryUrls = array();
            if(!is_null($gallery) and $gallery->getSize()) {

                $thumbnail  = $product->getThumbnail();
                $smallImage = $product->getSmallImage();
                $baseImage  = $product->getImage();

                foreach ($gallery as $_image) {

                    //If image is disabled do not use
                    if((int)$_image->getDisabled() === 1) {
                        continue;
                    }

                    $_imageData = array();

                    $_imageData['position'] = (int)$_image->getPosition();

                    $_imageData['is_base']      = ($_image->getFile() == $baseImage ? 1 : 0);
                    $_imageData['is_small']     = ($_image->getFile() == $smallImage ? 1 : 0);
                    $_imageData['is_thumbnail'] = ($_image->getFile() == $thumbnail ? 1 : 0);

                    $_imageData['large']    = $_image->getUrl();
                    $_imageData['label']    = $_image->getLabel();

                    $imagesConf = Mage::getConfig()->getNode(self::IMAGES_CONF_PATH)->asArray();

                    foreach ($imagesConf as $code => $size) {

                        $_size  = explode('x', $size);
                        $width  = $_size[0];
                        $height = $_size[1];

                        $thumb = Mage::helper('bakerloo_restful')->getResizedImageUrl($product->getId(), $this->getStoreId(), $_image->getFile(), (int)$width, (int)$height);
                        $_imageData[$code] = (string)$thumb;
                    }

                    $galleryUrls[] = $_imageData;

                    $_imageData = null;
                    $thumb      = null;

                    unset($_imageData);
                    unset($thumb);
                }
            }

            $result['images']                  = $galleryUrls;
            $result['description']             = (string) $product->getDescription();
            $result['short_description']       = (string) $product->getShortDescription();
            $result['use_description']         = (string) Mage::helper('bakerloo_restful')->config('catalog/description', $this->getStoreId());
            $result['last_update']             = $product->getUpdatedAt();
            $result['name']                    = $product->getName();
            $result['price']                   = $this->_getProductPrice($product);
            $result['product_id']              = (int) $product->getId();
            $result['sku']                     = $product->getSku();
            $result['barcode']                 = (string) Mage::helper('bakerloo_restful')->getProductBarcode($product->getId(), $this->getStoreId());
            $result['special_price']           = (float) $product->getSpecialPrice();
            $result['special_price_from_date'] = (string) $product->getSpecialFromDate();
            $result['special_price_to_date']   = (string) $product->getSpecialToDate();
            $result['store_id']                = (int) $product->getStoreId();
            $result['tax_class']               = (int) $product->getTaxClassId();
            $result['visibility']              = (int) $product->getVisibility();
            $result['status']                  = (int) $product->getStatus();
            $result['type']                    = $product->getTypeId();
            $result['categories']              = $this->_getCategories($product);
            $result['tier_pricing']            = $this->_getTierPrice($product);
            $result['group_pricing']           = $this->_getGroupPrice($product);

            //Adding cross sell, up sell and related products
            $result = $this->_addRelatedProductsData($product, $result);

            //configurable details
            $associatedProductsArray = array();
            $attributeOptions        = array();

            if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {

                $attributeConfig = $this->getAttributesConfig($product);

                //attributes
                $attributesData = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
                foreach ($attributesData as $productAttribute) {
                    $attributeValues = array();
                    foreach ($productAttribute['values'] as $attribute) {
                        $attributeValues[] = array(
                                                   'label'         => (string)$attribute['label'],
                                                   'value_index'   => (int)$attribute['value_index'],
                                                   'pricing_value' => (float)$attribute['pricing_value'],
                                                   'is_percent'    => (int)$attribute['is_percent']
                                                  );
                    }

                    //Attribute config for dependencies
                    $config = array();
                    if(isset($attributeConfig[$productAttribute['attribute_code']]['options'])) {
                        $config = $attributeConfig[$productAttribute['attribute_code']]['options'];
                    }

                    $attributeOptions[] = array(
                                                'attribute_code'  => $productAttribute['attribute_code'],
                                                'attribute_label' => $productAttribute['label'],
                                                'values'          => $attributeValues,
                                                'config'          => $config
                    );
                }

                unset($attributeConfig);

            }
            else if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {

                $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);

                foreach($associatedProducts as $_child) {
                    $associatedProductsArray []= (int)$_child->getId();
                }

            }
            else if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                $product->getTypeInstance(true)->setStoreFilter($this->getStoreId(), $product);

                $optionCollection = $product->getTypeInstance(true)->getOptionsCollection($product);

                $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
                    $product->getTypeInstance(true)->getOptionsIds($product),
                    $product
                );

                $optionsArray = $optionCollection->appendSelections($selectionCollection, false, false);

                $bundleAttributeOptions = array();
                $selected               = array();

                foreach ($optionsArray as $_option) {
                    if (!$_option->getSelections()) {
                        continue;
                    }

                    $option = array (
                        'id'         => (int)$_option->getOptionId(),
                        'title'      => $_option->getTitle(),
                        'type'       => (string)$_option->getType(),
                        'required'   => (int)$_option->getRequired(),
                        'position'   => (int)$_option->getPosition(),
                        'selections' => array()
                    );

                    $selectionCount = count($_option->getSelections());

                    foreach ($_option->getSelections() as $_selection) {
                        $_qty = !($_selection->getSelectionQty()*1)?'1':$_selection->getSelectionQty()*1;
                        $selection = array (
                            'id'           => (int)$_selection->getSelectionId(),
                            'qty'          => ($_qty * 1),
                            'canChangeQty' => (int)$_selection->getSelectionCanChangeQty(),
                            'price'        => Mage::helper('core')->currency($_selection->getFinalPrice(), false, false),
                            'priceValue'   => Mage::helper('core')->currency($_selection->getSelectionPriceValue(), false, false),
                            'priceType'    => $_selection->getSelectionPriceType(),
                            'tierPrice'    => $_selection->getTierPrice(),
                            'name'         => $_selection->getName(),
                            'product_id'   => (int)$_selection->getId(),
                            'position'     => (int)$_selection->getPosition(),
                            'is_default'   => (int)$_selection->getIsDefault(),
                        );
                        /*$responseObject = new Varien_Object();
                        $args = array('response_object'=>$responseObject, 'selection'=>$_selection);
                        Mage::dispatchEvent('bundle_product_view_config', $args);
                        if (is_array($responseObject->getAdditionalOptions())) {
                            foreach ($responseObject->getAdditionalOptions() as $o=>$v) {
                                $selection[$o] = $v;
                            }
                        }*/
                        $option['selections'][] = $selection;

                        if (($_selection->getIsDefault() || ($selectionCount == 1 && $_option->getRequired())) && $_selection->isSalable()) {
                            $selected[$_option->getId()][] = $_selection->getSelectionId();
                        }
                    }
                    $bundleAttributeOptions[] = $option;
                }

                $result['bundle_option'] = $bundleAttributeOptions;
                $price_type = 'dynamic';
                if($product->getPriceType()){
                    $price_type = 'fixed';
                }
                $result['price_type'] = $price_type;

            }

            $result['attributes'] = $attributeOptions;
            $result['children']   = $associatedProductsArray;

            //Custom Options
            $customOptions = array();
            $options       = $product->getOptions();

            if(count($options)) {
                $customOptions = $this->_getProductCustomOptions($product, $options);
            }

            $result['options'] = $customOptions;

            //Gift Card products specific options
            if("giftcard" == $product->getTypeId()) {

                $allowOpenAmount = ((int)$product->getAllowOpenAmount() === 1 ? true : false);

                $giftCardTypeLabel = 'Virtual';
                if((int)$product->getGiftcardType() === 1) {
                    $giftCardTypeLabel = 'Physical';
                }
                else {
                    if((int)$product->getGiftcardType() === 2) {
                        $giftCardTypeLabel = 'Combined';
                    }
                }

                $result['gift_card_options'] = array(
                    'type'              => (int)$product->getGiftcardType(), //0-Virtual, 1-Physical, 2-Combined
                    'type_label'        => Mage::helper('bakerloo_restful')->__($giftCardTypeLabel),
                    'amounts'           => $product->getGiftcardAmounts(),
                    'allow_open_amount' => $allowOpenAmount,
                );

                if($allowOpenAmount) {
                    $result['gift_card_options']['open_amount_min'] = (is_null($product->getOpenAmountMin()) ? 0.0000 : $product->getOpenAmountMin());
                    $result['gift_card_options']['open_amount_max'] = (is_null($product->getOpenAmountMax()) ? 0.0000 : $product->getOpenAmountMax());
                }
            }

            //Additional attributes
            $additionalAttributesConfig = (string) Mage::helper('bakerloo_restful')->config('catalog/additional_attributes', $this->getStoreId());
            if(!empty($additionalAttributesConfig)) {
                $attributes = explode(',', $additionalAttributesConfig);

                if(is_array($attributes) && !empty($attributes)) {
                    $additionalAttributeData = array();

                    foreach($attributes as $_attributeCode) {

                        if(!strlen($_attributeCode)) {
                            continue;
                        }

                        $_attributeValue = $product->getAttributeText($_attributeCode);
                        if(!$_attributeValue) {

                            $method = 'get' . uc_words($_attributeCode, '');
                            if( is_callable(array($product, $method)) ) {
                                $_attributeValue = $product->$method();
                            }

                            if(!$_attributeValue) {
                                $_attributeValue = '';
                            }

                        }

                        $_attr = $product->getResource()->getAttribute($_attributeCode);

                        $additionalAttributeData []= array(
                            'name'  => $_attributeCode,
                            'label' => $_attr->getFrontendLabel(),
                            'type'  => $_attr->getFrontendInput(),
                            'value' => $_attributeValue,
                        );

                    }

                    $result['additional_attributes'] = $additionalAttributeData;
                }
            }


        }

        return $this->returnDataObject($result);

    }

    private function _getProductCustomOptions(Mage_Catalog_Model_Product $product, $options) {

        $customOptions = array();

        $scope = (int) Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE);
        foreach ($options as $option) {
            /* @var $option Mage_Catalog_Model_Product_Option */

            $value = array();

            $value['option_id']  = (int)$option->getOptionId();
            $value['title']      = (string)$option->getTitle();
            $value['type']       = (string)$option->getType();
            $value['is_require'] = (int)$option->getIsRequire();
            $value['sort_order'] = (int)$option->getSortOrder();

            if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {

                $i = 0;
                $itemCount = 0;
                foreach ($option->getValues() as $_value) {
                    /* @var $_value Mage_Catalog_Model_Product_Option_Value */
                    $value['option_values'][$i] = array(
                        'option_type_id' => (int)$_value->getOptionTypeId(),
                        'title'          => (string)$_value->getTitle(),
                        'price'          => (float)$this->getPriceValue($_value->getPrice(), $_value->getPriceType()),
                        'price_type'     => (string)$_value->getPriceType(),
                        'sku'            => (string)$_value->getSku(),
                        'sort_order'     => (int)$_value->getSortOrder(),
                    );

                    $i++;
                }
            }
            else {
                $value['price']          = (float)$this->getPriceValue($option->getPrice(), $option->getPriceType());
                $value['price_type']     = (string)$option->getPriceType();
                $value['sku']            = (string)$option->getSku();
                $value['max_characters'] = (int)$option->getMaxCharacters();

            }

            $customOptions[] = $value;

        }

        return $customOptions;

    }

    public function getAttributesConfig($_product) {
        $attributes = array();
        $options    = array();

        $products    = array();
        $allProducts = $_product->getTypeInstance(true)
            ->getUsedProducts(null, $_product);

        foreach ($allProducts as $product) {
            //if ($product->isSaleable()) {
                $products[] = $product;
            //}
        }

        $allowAttributes = $_product->getTypeInstance(true)
            ->getConfigurableAttributes($_product);

        foreach ($products as $product) {
            $productId  = $product->getId();

            foreach ($allowAttributes as $attribute) {
                $productAttribute = $attribute->getProductAttribute();

                if(!is_object($productAttribute)) {
                    Mage::throwException("Attribute error: " . $attribute->getLabel() . '-' . $attribute->getProductId());
                }

                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttribute->getId()])) {
                    $options[$productAttribute->getId()] = array();
                }

                if (!isset($options[$productAttribute->getId()][$attributeValue])) {
                    $options[$productAttribute->getId()][$attributeValue] = array();
                }
                $options[$productAttribute->getId()][$attributeValue][] = (int)$productId;
            }
        }

        foreach ($allowAttributes as $attribute) {
            $productAttribute = $attribute->getProductAttribute();

            if(!is_object($productAttribute)) {
                Mage::throwException("Attribute error: " . $attribute->getLabel() . '-' . $attribute->getProductId());
            }

            $attributeId = $productAttribute->getId();
            $info = array(
                'id'        => (int)$productAttribute->getId(),
                'attribute_code' => $productAttribute->getAttributeCode(),
                'options'   => array()
            );

            $optionPrices = array();
            $prices = $attribute->getPrices();
            if (is_array($prices)) {
                foreach ($prices as $value) {
                    if(!isset($options[$attributeId][$value['value_index']])) {
                        continue;
                    }

                    $info['options'][] = array(
                        'value_index'   => (int)$value['value_index'],
                        'products'      => isset($options[$attributeId][$value['value_index']]) ? $options[$attributeId][$value['value_index']] : array(),
                    );
                }
            }
            $attributes[$productAttribute->getAttributeCode()] = $info;
        }

        return $attributes;
    }

    public function getPriceValue($value) {
        return number_format($value, 2, null, '');
    }

    private function _addRelatedProductsData(Mage_Catalog_Model_Product $product, array $result) {

        $related = array(
                         'cross_sell' => 'getCrossSellProducts',
                         'related'    => 'getRelatedProducts',
                         'up_sell'    => 'getUpSellProducts'
                        );

        foreach ($related as $key => $method) {

            $products = array();

            $related = $product->{$method}();
            foreach ($related as $prod) {
                $products[] = array('product_id' => (int)$prod->getId());
            }

            $result [$key]= $products;
        }

        return $result;
    }

    private function _getTierPrice($product) {
        return $this->_priceStruct($product, 'tier_price');
    }

    private function _getGroupPrice($product) {
        if(version_compare(Mage::getVersion(), '1.7.0.0', '<')) {
            return array();
        }

        return $this->_priceStruct($product, 'group_price');
    }

    private function _priceStruct($product, $dataType) {
        $dataPrice = array();

        $dataPriceData = $product->getData($dataType);

        if(is_array($dataPriceData) && !empty($dataPriceData)) {
            foreach ($dataPriceData as $_tprice) {
                $_tprice['price_id']          = (int)$_tprice['price_id'];
                $_tprice['website_id']        = (int)$_tprice['website_id'];
                $_tprice['all_groups']        = (int)$_tprice['all_groups'];
                $_tprice['customer_group_id'] = (int)$_tprice['cust_group'];

                unset($_tprice['cust_group']);

                if(isset($_tprice['price_qty'])) {
                    $_tprice['price_qty'] = (float)$_tprice['price_qty'];
                }

                $_tprice['price']             = (float)$_tprice['price'];
                $_tprice['website_price']     = (float)$_tprice['website_price'];

                $dataPrice [] = $_tprice;
            }
        }

        return $dataPrice;
    }

    /**
     * Retrieve DELETED or removed from website products.
     *
     * @return Collection data.
     */
    public function trashed() {
        $this->checkGetPermissions();

        $this->_model = 'bakerloo_restful/catalogtrash';

        //get page
        $page = $this->_getQueryParameter('page');
        if(!$page) {
            $page = 1;
        }

        $myFilters = array();
        $since     = $this->_getQueryParameter('since');
        if(!is_null($since)) {
            array_push($myFilters, "updated_at,gt,{$since}");
        }

        $filters = $this->_getQueryParameter('filters');
        if(is_null($filters)) {
            $filters = $myFilters;
        }
        else {
            $filters = array_merge($filters, $myFilters);
        }

        return $this->_getAllItems($page, $filters);

    }

    /**
     * Retrieve product price correctly from real object.
     *
     * @param $product
     * @return float
     */
    private function _getProductPrice($product) {

        $price = $product->getPrice();

        //Avoid price tricks from this module, just give me the configurable price.
        if($product instanceof OrganicInternet_SimpleConfigurableProducts_Catalog_Model_Product) {
            $_product = new Mage_Catalog_Model_Product();
            $_product->setPriceCalculation(false);
            $_product->load($product->getId());

            $price = $_product->getPrice();
        }

        return (float)$price;
    }

    /**
     * Return categories with product position data.
     *
     * @param $product
     * @return array
     */
    public function _getCategories($product) {
        $cats = $product->getCategoryIds();

        $categories = array();

        for($i=0;$i<count($cats);$i++) {
            $categoryId = $cats[$i];

            $myCategoryData = array(
              'category_id' => $categoryId,
              'position'    => 0,
            );

            $category  = new Varien_Object( array('id' => $categoryId) );
            $positions = Mage::getResourceModel('catalog/category')->getProductsPosition($category);

            if(!empty($positions)) {

                $exists = array_key_exists(((int)$product->getId()), $positions);
                if($exists) {
                    $myCategoryData['position'] = (int)$positions[$product->getId()];
                }

            }

            $categories []= $myCategoryData;
        }

        return $categories;
    }

}
