<?php 

	class Webkul_Preorder_Model_Product extends Mage_Catalog_Model_Product {

		public function isSalable() {
			$flag=0;
			$productId = $this->getId();
			$typeArray = array('bundle','grouped','configurable');
			$helper = Mage::helper("preorder");
			$productType = $this->getdata("type_id");

			if($productType=="grouped") {
				if($helper->isGroupedProductPreOrder($productId)) {
					$flag=1;
				}
			} elseif($productType=="bundle") {
				if($helper->isBundleProductPreOrder($productId)) {
					$flag=1;
				}
			} elseif($productType=="configurable"){
				if($helper->isConfigurableProductPreOrder($productId)) {
					$flag=1;
				}
			} else {
				if($helper->isPreorder($productId)) {
					$flag=1;
				}
			}

			if($flag==1) {
				return true;
			} else {
				Mage::dispatchEvent('catalog_product_is_salable_before', array(
					'product'		=>		$this
				));

				$salable = $this->isAvailable();

				$object = new Varien_Object(array(
					'product'		=>		$this,
					'is_salable'	=>		$salable
				));
				Mage::dispatchEvent('catalog_product_is_salable_after', array(
					'product'		=>		$this,
					'salable'		=>		$object
				));
				return $object->getIsSalable();
			}
		}
	}