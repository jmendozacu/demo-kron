<?php

  class Webkul_Preorder_Block_Catalog_Product_View_Type_Bundle_Option_Multi
      extends Mage_Bundle_Block_Catalog_Product_View_Type_Bundle_Option_Multi
  {
      /**
       * Set template
       *
       * @return void
       */
      protected function _construct() {
          $this->setTemplate('bundle/catalog/product/view/type/bundle/option/multi.phtml');
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
          $priceTitle = $_selection->getSelectionQty()*1 . ' x ' . $this->escapeHtml($_selection->getName());

          $priceTitle .= ' &nbsp; ' . ($includeContainer ? '<span class="price-notice">' : '')
              . '+' . $this->formatPriceString($price, $includeContainer)
              . ($includeContainer ? '</span>' : '');

          if($helper->isPreorder($productId)) {
            $priceTitle .= ' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Preorder ';
          }

          return  $priceTitle;
      }
  }
