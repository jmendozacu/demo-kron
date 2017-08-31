<?php

class Ebizmarts_BakerlooRestful_Model_V1_Products extends Ebizmarts_BakerlooRestful_Model_V1_Api {

    protected $_model = "catalog/product";

    const IMAGES_CONF_PATH = 'default/bakerloorestful/product/imagesizes';

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

    protected function _getCollection() {

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

            //Images
            $galleryUrls = array();
            if(!is_null($gallery) && $gallery->getSize()) {

                $thumbnail  = $product->getThumbnail();
                $smallImage = $product->getSmallImage();
                $baseImage  = $product->getImage();

                foreach ($gallery as $_image) {

                    //If image is disabled do not use
                    if((int)$_image->getDisabled() === 1) {
                        continue;
                    }

                    $_imageData = array();

                    $_imageData['large'] = $_image->getUrl();
                    $_imageData['label'] = $_image->getLabel();

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

            unset($gallery);

            $result['images']            = $galleryUrls;
            $result['description']       = (string) $product->getDescription();
            $result['short_description'] = (string) $product->getShortDescription();
            $result['last_update']       = $product->getUpdatedAt();
            $result['name']              = $product->getName();
            $result['price']             = (float) $product->getPrice();
            $result['product_id']        = (int) $product->getId();
            $result['sku']               = $product->getSku();
            $result['barcode']           = (string) $product->getBarcode();
            $result['special_price']     = (float) $product->getSpecialPrice();
            $result['store_id']          = (int) $product->getStoreId();
            $result['tax_class']         = (int) $product->getTaxClassId();
            $result['visibility']        = (int) $product->getVisibility();
            $result['status']            = (int) $product->getStatus();
            $result['type']              = $product->getTypeId();
            $result['categories']        = array_map('intval', $product->getCategoryIds());
            $result['tier_pricing']      = $this->_getTierPrice($product);

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
                                                   'label' => (string)$attribute['label'],
                                                   'value_index' => (int)$attribute['value_index'],
                                                   'pricing_value' => (float)$attribute['pricing_value'],
                                                   'is_percent' => (int)$attribute['is_percent']
                                                  );
                    }

                    //Attribute config for dependencies
                    $config = array();
                    if(isset($attributeConfig[$productAttribute['attribute_code']]['options'])) {
                        $config = $attributeConfig[$productAttribute['attribute_code']]['options'];
                    }

                    $attributeOptions[] = array('attribute_code' => $productAttribute['attribute_code'],
                                                'values'  => $attributeValues,
                                                'config' => $config);
                }

                unset($attributeConfig);

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
        }

        return $result;

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

        $tierPrice = array();

        $tierPriceData = $product->getData('tier_price');

        if(is_array($tierPriceData) && !empty($tierPriceData)) {
            foreach ($tierPriceData as $_tprice) {
                $_tprice['price_id']          = (int)$_tprice['price_id'];
                $_tprice['website_id']        = (int)$_tprice['website_id'];
                $_tprice['all_groups']        = (int)$_tprice['all_groups'];
                $_tprice['customer_group_id'] = (int)$_tprice['cust_group'];
                unset($_tprice['cust_group']);
                $_tprice['price_qty']         = (float)$_tprice['price_qty'];
                $_tprice['price']             = (float)$_tprice['price'];
                $_tprice['website_price']     = (float)$_tprice['website_price'];

                $tierPrice [] = $_tprice;
            }
        }

        return $tierPrice;
    }

    /**
     * Retrieve DELETED or removed from website products.
     *
     * @return Collection data.
     */
    public function trashed() {
        $this->checkGetPermissions();

        $trash = Mage::getModel('bakerloo_restful/catalogtrash')
                    ->getCollection()
                    ->addFieldToFilter('store_id',
                                                    array(
                                                        array('eq'   => $this->getStoreId()),
                                                        array('null' => true),
                                                    ));

        $since = $this->_getQueryParameter('since');
        if(!is_null($since)) {
            $trash->addFieldToFilter("updated_at", array("gt" => $since));
        }

        $items = $trash->getData();

        return $this->_getCollectionPageObject($items, 1, null, null, count($items));
    }
}