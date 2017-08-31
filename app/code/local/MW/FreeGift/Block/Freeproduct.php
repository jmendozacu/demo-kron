<?php
class MW_FreeGift_Block_Freeproduct extends Mage_Core_Block_Template
{
	public function getFreeCatalogGifts()
	{
		$currentProduct = Mage::registry('current_product') ? Mage::registry('current_product') : false;
		if (!$currentProduct)
			$currentProduct = $this->getRequest()->getParam('id') ? Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id')) : false;
		if ($currentProduct) {          
			return Mage::getModel('freegift/product')->init($currentProduct)->getFreeGifts();
		}
		return false;
	}
	
	public function _toHtml()
	{
		if (!Mage::getStoreConfig('freegift/config/enabled'))
			return '';
		if (!sizeof($this->getFreeCatalogGifts()))
			return '';
		$html = $this->renderView();
		return $html;
	}
	
	public function getRuleFreeProductId($productId)
	{//
		$rules = Mage::getModel('freegift/product')->getCollection();
		foreach ($rules as $rule) {
			$productIds = explode(',', $rule->getData('gift_product_ids'));
			if (in_array($productId, $productIds)) {
				return $rule;
			}
		}
		return false;
	}
	
	public function getAllRule()
	{
		$listProductId = array();
		foreach (Mage::getModel('freegift/product')->getCollection() as $rule) {
			$listProductId[] = $rule->getData('product_id');
		}
		return array_unique($listProductId);
	}
	
	public function getFreeCatalogGiftsByProduct($product)
	{
		$listProductFreeGifts = array();
		if ($product) {
			$productIds = Mage::getModel('freegift/product')->init($product)->getFreeGifts();
            return $productIds;
			if(is_array($productIds)){						
				foreach($productIds as $productId){
					$dis = Mage::getModel('freegift/observer')->displayFreeGift($productId);
					if($dis)$listProductFreeGifts[] = $productId;						
					}			
			}
		}
		return $listProductFreeGifts;
	}
	
	public function getRuleProductId($productId)
	{
		$rules = Mage::getModel('freegift/product')->getCollection();
		foreach ($rules as $rule) {
			$productIds = $rule->getData('product_id');
			if ($productId == $productIds) {
				return $rule;
			}
		}
		return false;
	}
	
	public function getFreeGiftCatalog($product){
		/*$lisProductId = $this->getAllRule();*/
		$isShowFreeGift = Mage::getStoreConfig('freegift/config/showfreegiftoncategory');
		$productIds = $this->getFreeCatalogGiftsByProduct($product);
		/*$rule = $this->getRuleProductId($product->getId());

		$discount = 1;
		$timesUsed = 0;
		if($rule){
			$discount = $rule->getDiscountQty();	
			$timesUsed = $rule->getTimesUsed();	
			if($discount == 0){
				$timesUsed = -1;
			}
		}*/

		if($productIds && $isShowFreeGift){
		/*if($productIds && $isShowFreeGift && ($discount > $timesUsed)){*/
			return $productIds;
		}
		return false;
	}
} 
