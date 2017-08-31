<?php
class MW_FreeGift_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{

    /**
     * Retrieve url for direct adding product to cart
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = array())
    {
        if ($this->hasCustomAddToCartUrl()) {
            return $this->getCustomAddToCartUrl();
        }

        if ($this->getRequest()->getParam('wishlist_next')){
            $additional['wishlist_next'] = 1;
        }

        $addUrlKey = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $addUrlValue = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => false));
        $additional[$addUrlKey] = Mage::helper('core')->urlEncode($addUrlValue);

        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }
    
    public function getSubmitUrl($product, $additional = array())
    {
    	
    	$layout = Mage::getSingleton('core/layout');

	    //get block object
	    $block = $layout->createBlock('freegift/product');
	        
	    $mw_free_gift = 0;
	    $mw_free_gift = $this->getRequest()->getParam('mw_freegift');
	    $free_gift = $this->getRequest()->getParam('freegift');
	    if($mw_free_gift || $free_gift){
		    $productId = $product->getId();
		    $mw_productIds = $block->getFreeProducts();
		    $rule = $block->getRuleByFreeProductId($productId);
		    if($rule) {
		    	$additional['freegift']	= 1;
		    	$additional['apllied_rule'] = $rule->getId();
		    }	
	    }    
    	
        $submitRouteData = $this->getData('submit_route_data');
        if ($submitRouteData) {
            $route = $submitRouteData['route'];
            $params = isset($submitRouteData['params']) ? $submitRouteData['params'] : array();
            $submitUrl = $this->getUrl($route, array_merge($params, $additional));
        } else {
            $submitUrl = $this->getAddToCartUrl($product, $additional);
        }
        return $submitUrl;
    }
}
