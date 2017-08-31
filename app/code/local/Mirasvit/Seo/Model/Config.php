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


class Mirasvit_Seo_Model_Config
{
    const NO_TRAILING_SLASH = 1;
    const TRAILING_SLASH    = 2;

    const URL_FORMAT_SHORT = 1;
    const URL_FORMAT_LONG  = 2;

    const NOINDEX_NOFOLLOW = 1;
    const NOINDEX_FOLLOW   = 2;
    const INDEX_NOFOLLOW   = 3;

    const CATEGYRY_RICH_SNIPPETS_PAGE     = 1;
    const CATEGYRY_RICH_SNIPPETS_CATEGORY = 2;

    const PRODUCTS_WITH_REVIEWS_NUMBER    = 1;
    const REVIEWS_NUMBER                  = 2;

    const BREADCRUMBS_WITH_SEPARATOR      = 1;
    const BREADCRUMBS_WITHOUT_SEPARATOR   = 2;

    public function isAddCanonicalUrl()
    {
        return Mage::getStoreConfig('seo/general/is_add_canonical_url');
    }

    public function getCrossDomainStore()
    {
        return Mage::getStoreConfig('seo/general/crossdomain');
    }

    public function getCanonicalUrlIgnorePages()
    {
        $pages = Mage::getStoreConfig('seo/general/canonical_url_ignore_pages');
        $pages = explode("\n", trim($pages));
        $pages = array_map('trim',$pages);

        return $pages;
    }

    public function getNoindexPages()
    {
        $pages = Mage::getStoreConfig('seo/general/noindex_pages2');
        $pages = unserialize($pages);
        $result = array();
        if (is_array($pages)) {
            foreach ($pages as $value) {
                $result[] = new Varien_Object($value);
            }
        }
        return $result;
    }

    public function isAlternateHreflangEnabled($store)
    {
        return Mage::getStoreConfig('seo/general/is_alternate_hreflang', $store);
    }

    public function getHreflangLocaleCode($store)
    {
        return trim(Mage::getStoreConfig('seo/general/hreflang_locale_code', $store));
    }

    public function isPagingPrevNextEnabled()
    {
        return Mage::getStoreConfig('seo/general/is_paging_prevnext');
    }

    public function isCategoryMetaTagsUsed()
    {
        return Mage::getStoreConfig('seo/general/is_category_meta_tags_used');
    }

    public function isProductMetaTagsUsed()
    {
        return Mage::getStoreConfig('seo/general/is_product_meta_tags_used');
    }

///////////// Rich Snippets and Opengraph
    public function isRichSnippetsEnabled($store)
    {
        return Mage::getStoreConfig('seo/snippets/is_rich_snippets', $store);
    }

    public function getRichSnippetsBrandAttributes()
    {
        return $this->_prepereAttributes(Mage::getStoreConfig('seo/snippets/rich_snippets_brand_config'));
    }

    public function getRichSnippetsModelAttributes()
    {
        return $this->_prepereAttributes(Mage::getStoreConfig('seo/snippets/rich_snippets_model_config'));
    }

    protected function _prepereAttributes($attributes) {
        $attributes = strtolower(trim($attributes));
        $attributes = explode(",", trim($attributes));
        $attributes = array_map('trim',$attributes);
        $attributes = array_diff($attributes, array(null));

        return $attributes;
    }

    public function isDeleteWrongSnippets($store)
    {
        return Mage::getStoreConfig('seo/snippets/delete_wrong_snippets', $store);
    }

    public function getCategoryRichSnippets($store)
    {
        return Mage::getStoreConfig('seo/snippets/category_rich_snippets', $store);
    }

    public function getCategoryRichSnippetsPriceText($store)
    {
        return Mage::getStoreConfig('seo/snippets/category_rich_snippets_price_text', $store);
    }

    public function getCategoryRichSnippetsRatingText($store)
    {
        return Mage::getStoreConfig('seo/snippets/category_rich_snippets_rating_text', $store);
    }

    public function getCategoryRichSnippetsRewiewCountText($store)
    {
        return Mage::getStoreConfig('seo/snippets/category_rich_snippets_rewiew_count_text', $store);
    }

    public function getRichSnippetsRewiewCount($store)
    {
        return Mage::getStoreConfig('seo/snippets/category_rich_snippets_rewiew_count', $store);
    }

     public function isHideCategoryRichSnippets($store)
    {
        return Mage::getStoreConfig('seo/snippets/hide_category_rich_snippets', $store);
    }

    public function isBreadcrumbs($store)
    {
        return Mage::getStoreConfig('seo/snippets/is_breadcrumbs', $store);
    }

    public function getBreadcrumbsSeparator($store)
    {
        $separator = trim(Mage::getStoreConfig('seo/snippets/breadcrumbs_separator', $store));
        if(empty($separator)) {
            return false;
        }
        return $separator;
    }

    public function isOpenGraphEnabled()
    {
        return Mage::getStoreConfig('seo/snippets/is_opengraph');
    }

///////////// SEO URL
    public function isEnabledSeoUrls()
    {
        return Mage::getStoreConfig('seo/url/layered_navigation_friendly_urls');
    }

    public function getTrailingSlash()
    {
        return Mage::getStoreConfig('seo/url/trailing_slash');
    }

    public function getProductUrlFormat()
    {
       return Mage::getStoreConfig('seo/url/product_url_format');
    }

    public function getProductUrlKey($store)
    {
       return Mage::getStoreConfig('seo/url/product_url_key', $store);
    }

    public function isEnabledTagSeoUrls()
    {
        return Mage::getStoreConfig('seo/url/tag_friendly_urls');
    }

    public function isEnabledReviewSeoUrls()
    {
        return Mage::getStoreConfig('seo/url/review_friendly_urls');
    }

///////////// IMAGE
    public function getIsEnableImageFriendlyUrls()
    {
        return Mage::getStoreConfig('seo/image/is_enable_image_friendly_urls');
    }

    public function getImageUrlTemplate()
    {
        return Mage::getStoreConfig('seo/image/image_url_template');
    }
    public function getIsEnableImageAlt()
    {
        return Mage::getStoreConfig('seo/image/is_enable_image_alt');
    }

    public function getImageAltTemplate()
    {
        return Mage::getStoreConfig('seo/image/image_alt_template');
    }
}
