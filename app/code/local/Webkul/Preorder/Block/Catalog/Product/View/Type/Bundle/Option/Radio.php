<?php

    class Webkul_Preorder_Block_Catalog_Product_View_Type_Bundle_Option_Radio extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Radio {
       
        protected function _construct() {
            $this->setTemplate('preorder/bundle/catalog/product/view/type/bundle/option/radio.phtml');
        }

        public function getSelectionTitlePrice($_selection, $includeContainer = true) {
            $helper = Mage::helper("preorder");
            $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection, 1);
            
            $_product = Mage::getModel('catalog/product')->loadByAttribute('name',$_selection->getName());
            $productId = $_product->getId();
            $preorderPercent = $helper->getPreorderPercent();
            if($helper->isPreorder($productId)) {
                $price=($price*$preorderPercent)/100;
            }

            $this->setFormatProduct($_selection);
            $priceTitle = $this->escapeHtml($_selection->getName());
            $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
                . '+' . $this->formatPriceString($price, $includeContainer)
                . ($includeContainer ? '</span>' : '');

            if($helper->isPreorder($productId)) {
                $priceTitle .= ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Preorder ';
            }
            return $priceTitle;
        }
    }
