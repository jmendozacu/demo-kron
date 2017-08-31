<?php

    class Webkul_Preorder_Block_Product_View_Type_Configurable extends Mage_Catalog_Block_Product_View_Type_Configurable {
        
        public function getAllowProducts() {
            if (!$this->hasAllowProducts()) {
                $products = array();
                $skipSaleableCheck = Mage::helper('catalog/product')->getSkipSaleableCheck();
                $allProducts = $this->getProduct()->getTypeInstance(true)
                    ->getUsedProducts(null, $this->getProduct());
                foreach ($allProducts as $product) {
                    $attr = $product->getResource()->getAttribute('wk_preorder');
                    $val_id = $attr->getSource()->getOptionId('Enable');
                    if($this->getProduct()->getdata('wk_preorder') != $val_id || Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId())->getIsInStock()==1){
                        if ($product->isSaleable()) {
                            $products[] = $product;
                        }
                    }
                    else{
                        $products[] = $product;
                    }
                }
                $this->setAllowProducts($products);
            }
            return $this->getData('allow_products');
        }
    }