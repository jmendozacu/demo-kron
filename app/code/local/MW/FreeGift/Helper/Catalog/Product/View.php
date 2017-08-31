<?php
/**
 * User: Anh TO
 * Date: 3/26/14
 * Time: 11:38 AM
 */

class MW_FreeGift_Helper_Catalog_Product_View extends Mage_Core_Helper_Abstract{
    public function prepareAndRender($productId, $params = null)
    {
        // Prepare data
        $productHelper = Mage::helper('freegift/catalog_product');
        if (!$params) {
            $params = new Varien_Object();
        }

        // Standard algorithm to prepare and rendern product view page
        $product = $productHelper->initProduct($productId, $params);
        if (!$product) {
            throw new Mage_Core_Exception($this->__('Product is not loaded'), $this->ERR_NO_PRODUCT_LOADED);
        }

        $buyRequest = $params->getBuyRequest();
        if ($buyRequest) {
            $productHelper->prepareProductOptions($product, $buyRequest);
        }

        if ($params->hasConfigureMode()) {
            $product->setConfigureMode($params->getConfigureMode());
        }

        if ($params->getSpecifyOptions()) {
            $notice = $product->getTypeInstance(true)->getSpecifyOptionMessage();
            Mage::getSingleton('catalog/session')->addNotice($notice);
        }


        return $product;
    }
}