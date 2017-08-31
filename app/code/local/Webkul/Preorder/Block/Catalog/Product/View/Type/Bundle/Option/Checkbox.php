<?php

    class Webkul_Preorder_Block_Catalog_Product_View_Type_Bundle_Option_Checkbox extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Checkbox {
   
        protected function _construct() {
            $this->setTemplate('preorder/bundle/catalog/product/view/type/bundle/option/checkbox.phtml');
        }
        
        public function getSelectionQtyTitlePrice($_selection, $includeContainer = true) {
            $helper = Mage::helper("preorder");
            $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection);
            $_product = Mage::getModel('catalog/product')->loadByAttribute('name',$_selection->getName());
            $productId = $_product->getId();
            $preorderPercent = $helper->getPreorderPercent();
            if($helper->isPreorder($productId)) {
               $price=($price*$preorderPercent)/100;
            }
            $this->setFormatProduct($_selection);
            $priceTitle = $_selection->getSelectionQty() * 1 . ' x ' . $this->escapeHtml($_selection->getName());
            $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
                . '+' . $this->formatPriceString($price, $includeContainer)
                . ($includeContainer ? '</span>' : '');

            if($helper->isPreorder($productId)) {
               $priceTitle .= "<span class='wk-preorder-item'>Preorder</span>";
            }
            return $priceTitle;
        }
    }