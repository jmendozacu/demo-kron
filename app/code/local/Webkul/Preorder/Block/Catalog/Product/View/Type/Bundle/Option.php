<?php
    
    class Webkul_Preorder_Block_Catalog_Product_View_Type_Bundle_Option extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option {

        public function getSelectionQtyTitlePrice($_selection, $includeContainer = true) {

            $price = $this->getProduct()->getPriceModel()->getSelectionPreFinalPrice($this->getProduct(), $_selection);
            $this->setFormatProduct($_selection);
            $priceTitle = $_selection->getSelectionQty()*1 . ' x ' . $this->escapeHtml($_selection->getName());

            $priceTitle .= 'test &nbsp; test' . ($includeContainer ? '<span class="price-notice">' : '')
                . '+' . $this->formatPriceString($price, $includeContainer)
                . ($includeContainer ? '</span>' : '');

            return  $priceTitle;
        }
    }