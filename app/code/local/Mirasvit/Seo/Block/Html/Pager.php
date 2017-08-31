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


class Mirasvit_Seo_Block_Html_Pager extends Mage_Page_Block_Html_Pager
{
    protected $_fullActionCode;

    protected function getFullActionCode() {
        if (!$this->_fullActionCode) {
            $request = Mage::app()->getRequest();
            $this->_fullActionCode = $request->getModuleName().'_'.$request->getControllerName().'_'.$request->getActionName();
        }

        return $this->_fullActionCode;
    }

    protected function getSeoReviewUrl($page, $limit) {
        $frontControllerRequest = Mage::app()->getFrontController()->getRequest();
        $params = $frontControllerRequest->getQuery();
        if ($page && $page == 1) {
            unset($params['p']);
        } elseif ($page) {
             $params['p'] = $page;
        }
        if ($limit) {
            $params['limit'] = $limit;
        }
        $originalPath = $frontControllerRequest->getOriginalPathInfo();
        $reviewPath = (substr($originalPath, 0, 1) == '/') ? substr($originalPath, 1) : $originalPath;

        return Mage::getUrl('',
            array(
                '_direct' => $reviewPath,
                '_query' => $params
            )
        );
    }

    public function getPageUrl($page)
    {
        //support of TM_Attributepages and Fishpig_AttributeSplash
        if ($this->getFullActionCode() == 'attributepages_page_view'
            || $this->getFullActionCode() == 'attributesplash_page_view'
            || $this->getFullActionCode() == 'splash_page_view') {
            return parent::getPageUrl($page);
        }
        if (Mage::helper('mstcore')->isModuleInstalled('Mirasvit_SeoFilter')
            && Mage::registry('current_category')
            && $this->getFullActionCode() != 'review_product_list'
            && $this->getFullActionCode() != 'tag_product_list') {
            if (Mage::getModel('seofilter/config')->isEnabled()) {
                if ($page == 1) {
                    $url = Mage::getModel('seofilter/catalog_layer_filter_item')->getSpeakingFilterUrl(FALSE, TRUE, array());

                    return $url;
                } else {
                    $url = Mage::getModel('seofilter/catalog_layer_filter_item')->getSpeakingFilterUrl(FALSE, TRUE, array($this->getPageVarName()=>$page));

                    return $url;
                }
            }
        }

        if ($this->getFullActionCode() == 'review_product_list' || $this->getFullActionCode() == 'tag_product_list') {
            return $this->getSeoReviewUrl($page, false);
        } else {
            if ($page == 1) {
                $params = Mage::app()->getFrontController()->getRequest()->getQuery();
                unset($params['p']);

                $urlParams['_use_rewrite'] = true;
                $urlParams['_escape']      = true;
                $urlParams['_query']       = $params;

                return $this->getUrl('*/*/*', $urlParams);
            } else {
                return $this->getPagerUrl(array($this->getPageVarName()=>$page));
            }
        }
    }

    public function getLimitUrl($limit)
    {
        if ($this->getFullActionCode() == 'review_product_list') {
            return $this->getSeoReviewUrl(false, $limit);
        } else {
            return parent::getLimitUrl($limit);
        }
    }
}
