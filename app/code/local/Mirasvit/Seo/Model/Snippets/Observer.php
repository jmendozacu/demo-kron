<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced SEO Suite
 * @version   1.2.0
 * @build     970
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Seo_Model_Snippets_Observer extends Varien_Object {
    var $appliedSnippets         = false;
    var $isProductPage           = false;
    var $isCategoryPage          = false;
    var $appliedCategorySnippets = false;

    function __construct() {
        if(Mage::app()->getFrontController()->getRequest()->getControllerName() === "product") {
            $this->isProductPage = true;
        }
        if(Mage::app()->getFrontController()->getRequest()->getControllerName() === "category"
           || Mage::app()->getFrontController()->getRequest()->getModuleName() === "amlanding") {
            $this->isCategoryPage = true;
        }
    }

    public function getConfig()
    {
        return Mage::getSingleton('seo/config');
    }

    public function addProductSnippets($e) {
        if($this->isProductPage
            && !$this->appliedSnippets
            && $e->getData('block')->getNameInLayout() == "product.info"
            && $this->getConfig()->isRichSnippetsEnabled(Mage::app()->getStore()->getId())) {

            $html = $e->getData('transport')->getHtml();
            $product = $e->getData('block')->getProduct();

            if ($this->getConfig()->isDeleteWrongSnippets(Mage::app()->getStore()->getId())) {
                $html = $this->_deleteWrongSnippets($html);
            }

            if ($htmlOfferFilter = $this->offerFilter($html,$product)) {
                $html = $htmlOfferFilter;
            }

            if ($htmlProductFilter = $this->productFilter($html,$product)) {
                $html = $htmlProductFilter;
            }

            if ($htmlAggregateRatingFilter = $this->aggregateRatingFilter($html,$product)) {
                $html = $htmlAggregateRatingFilter;
            }

            $e->getData('transport')->setHtml(
                $html
            );

            $this->appliedSnippets = true;
        } elseif ($this->isCategoryPage
                && !$this->appliedCategorySnippets
                && $e->getData('block')->getNameInLayout() == "product_list"
                && $this->getConfig()->getCategoryRichSnippets(Mage::app()->getStore()->getId()) == Mirasvit_Seo_Model_Config::CATEGYRY_RICH_SNIPPETS_PAGE) {
                    $productCollection = $e->getData('block')->getLoadedProductCollection();
                    Mage::register('category_product_for_snippets', $productCollection);
                    $this->appliedCategorySnippets = true;
        }

        if ($e->getData('block')->getNameInLayout() == "breadcrumbs"
            && $this->getConfig()->isBreadcrumbs(Mage::app()->getStore()->getId()) == Mirasvit_Seo_Model_Config::BREADCRUMBS_WITHOUT_SEPARATOR) {
                $html = $e->getData('transport')->getHtml();
                if ($crumbs = $this->breadcrumbsFilter($html)) {
                    $html = $crumbs;
                }
                $e->getData('transport')->setHtml(
                    $html
                );
        }
    }

    protected function _deleteWrongSnippets($html) { //maybe need improvement
        $pattern = array('/itemprop="(.*?)"/ims',
                        '/itemprop=\'(.*?)\'/ims',
                        '/itemtype="(.*?)"/ims',
                        '/itemtype=\'(.*?)\'/ims',
                        '/itemscope="(.*?)"/ims',
                        '/itemscope=\'(.*?)\'/ims',
                        '/itemscope=\'\'/ims',
                        '/itemscope=""/ims',
                        '/itemscope\s/ims',
                        );
        $html = preg_replace($pattern,'',$html);

        return $html;
    }

    protected function _getRichSnippetsAttributeValue($attributeArray, $product) {
        $attributeValue = false;
        foreach ($attributeArray as $attributeName) {
            if ($attribute = $product->getResource()->getAttribute($attributeName)) {
                $attributeValue = $attribute->getFrontend()->getValue($product);
            }
            if ($attributeValue && $attributeValue != 'No' && $attributeValue != 'Нет') {
                return $attributeValue;
            }
        }

        return false;
    }

    public function productFilter($html, $product) {
        $html = preg_replace('/\\"product\\-name\\"/i','"product-name" itemprop="name"',$html,1);
        $html = preg_replace('/\\"short\\-description\\"/i','"short-description" itemprop="description"',$html,1);

        $replacement = '';
        $attributeBrand = false;
        $attributeModel = false;
        if ($brands = $this->getConfig()->getRichSnippetsBrandAttributes()) {
            $attributeBrand = $this->_getRichSnippetsAttributeValue($brands, $product);
        }
        if ($models = $this->getConfig()->getRichSnippetsModelAttributes()) {
            $attributeModel = $this->_getRichSnippetsAttributeValue($models, $product);
        }
        if ($attributeBrand) {
            $replacement .= '<meta itemprop="brand" content="'.$attributeBrand.'" />';
        }
        if ($attributeModel) {
            $replacement .= '<meta itemprop="model" content="'.$attributeModel.'"/>';
        }
        if ($sku = $product->getSku()) {
            $replacement .= '<meta itemprop="sku" content="'.$sku.'"/>';
        }
        if ($image = Mage::helper('catalog/image')->init($product, 'image')) {
            $replacement .= '<meta itemprop="image" content="'.$image.'"/>';
        }

        if ($replacement) {
            $html = preg_replace('/\\<div class\\=\\"product\\-name\\" itemprop\\=\\"name\\"/i', $replacement . '<div class="product-name" itemprop="name"',$html,1);
        }

        $html = '<div itemscope itemtype="http://schema.org/Product">'.$html.'</div>';
        return $html;
    }

    public function offerFilter($html, $product)
    {
        $availability = "";
            if(method_exists ($product , "isAvailable" )) {
                $check = $product->isAvailable();
            } else {
                $check = $product->isInStock();
            }
            if ($check) {
                $availability .= '<link itemprop="availability" href="http://schema.org/InStock" />';
            } else {
                $availability .= '<link itemprop="availability" href="http://schema.org/OutOfStock" />';
            }

            $price = "";
            $currencyCode           = Mage::app()->getStore()->getCurrentCurrencyCode();
            $priceSymbol            = Mage::app()->getLocale()->currency($currencyCode)->getSymbol();
            $productFinalPrice = false;
            $priceModel  = $product->getPriceModel();
            if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                list($minimalPriceInclTax, $maximalPriceInclTax) = $priceModel->getPrices($product, null, true, false);
                if (($minimalPriceInclTax = $this->formatPrice($minimalPriceInclTax)) && $currencyCode) {
                    $productFinalPrice = $minimalPriceInclTax;
                }
            } elseif ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
                if (($minimalPriceValue = $this->formatPrice($this->getGroupedMinimalPrice($product))) && $currencyCode) {
                    $productFinalPrice = $minimalPriceValue;
                }
            } else {
                $finalPriceInclTax = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);
                if (($finalPriceInclTax = $this->formatPrice($finalPriceInclTax)) && $currencyCode) {
                    $productFinalPrice = $finalPriceInclTax;
                }
            }
        if(preg_match('/(special\\-price\\".*?)\\<span class\\=\\"price\\"(.*?)>(.*?)\\<\\/span\\>/ims',$html)) {
            if ($productFinalPrice) {
                $price = '<meta itemprop="priceCurrency" content="'.$currencyCode.'" />'.
                         '<meta itemprop="price" content="'.$productFinalPrice.'" />'.
                         '<span class="price" $2>$3</span>'
                        ;
                $replacement = '$1<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">'.$availability.$price.'</span>';
                $html = preg_replace('/(special\\-price\\".*?)\\<span class\\=\\"price\\"(.*?)\\>(.*?)\\<\\/span\\>/ims', $replacement, $html, 1);
            }
        } else {
            if ($productFinalPrice) {
                $price = '<meta itemprop="priceCurrency" content="'.$currencyCode.'" />'.
                         '<meta itemprop="price" content="'.$productFinalPrice.'" />'.
                         '<span class="price" $1>$2</span>'
                        ;
                $replacement = '<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">'.$availability.$price.'</span>';
                $html = preg_replace('/\\<span class\\=\\"price\\"(.*?)\\>(.*?)\\<\\/span\\>/ims', $replacement, $html, 1);
            }
        }

        return $html;
    }

    protected function formatPrice($price)
    {
        if (intval($price)) {
            $price = Mage::getModel('directory/currency')->format(
                $price,
                array('display'=>Zend_Currency::NO_SYMBOL),
                false
            );

            return $price;
        }

        return false;
    }

    public function aggregateRatingFilter($html, $product) {
        if (!is_object($product->getRatingSummary())) {
            return false;
        }
        if ($product->getRatingSummary()->getRatingSummary() && $product->getRatingSummary()->getReviewsCount()) {
            $ratingData = '';
            if ($ratingValue = number_format($product->getRatingSummary()->getRatingSummary()/100*5, 2)) {
                $ratingData .= '<meta itemprop="ratingValue" content="'.$ratingValue.'" />';
            }
            if ($reviewsCount = $product->getRatingSummary()->getReviewsCount()) {
                $ratingData .= '<meta itemprop="reviewCount" content="'.$reviewsCount.'" />';
            }
            $html = preg_replace('/\\<div class\\=\\"ratings\\"(.*?)\\>/ims','<div class="ratings" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">'.$ratingData,$html,1);
            return $html;
        } else {
            return false;
        }
    }

    public function breadcrumbsFilter($html) {
            if (strpos($html, 'class="breadcrumbs"') !== false) {
                $liTagCount = substr_count($html, '<li');
                $html = preg_replace('/\\<li/','<li typeof="v:Breadcrumb"',$html, $liTagCount-1); // we don't add v:Breadcrumb in the final tag
                $html = preg_replace('/\\<a/','<a rel="v:url" property="v:title"',$html);
                return $html;
            }
            return false;
    }
}