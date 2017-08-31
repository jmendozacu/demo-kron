<?php

    class Webkul_Preorder_Helper_Bundle_Catalog_Product_Configuration extends Mage_Bundle_Helper_Catalog_Product_Configuration implements Mage_Catalog_Helper_Product_Configuration_Interface {
    
        public function getSelectionQty($product, $selectionId) {
            $selectionQty = $product->getCustomOption('selection_qty_' . $selectionId);
            if ($selectionQty) {
                return $selectionQty->getValue();
            }
            return 0;
        }

        public function getSelectionFinalPrice(Mage_Catalog_Model_Product_Configuration_Item_Interface $item, $selectionProduct) {
            $selectionProduct->unsetData('final_price');
            return $item->getProduct()->getPriceModel()->getSelectionFinalTotalPrice(
                $item->getProduct(),
                $selectionProduct,
                $item->getQty() * 1,
                $this->getSelectionQty($item->getProduct(), $selectionProduct->getSelectionId()),
                false,
                true
            );
        }
        public function getBundleOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item) {
            $helper = Mage::helper("preorder");
            $options = array();
            $product = $item->getProduct();
            $typeInstance = $product->getTypeInstance(true);

            // get bundle options
            $optionsQuoteItemOption = $item->getOptionByCode('bundle_option_ids');
            $bundleOptionsIds = $optionsQuoteItemOption ? unserialize($optionsQuoteItemOption->getValue()) : array();
            if ($bundleOptionsIds) {
                /**
                * @var Mage_Bundle_Model_Mysql4_Option_Collection
                */
                $optionsCollection = $typeInstance->getOptionsByIds($bundleOptionsIds, $product);

                // get and add bundle selections collection
                $selectionsQuoteItemOption = $item->getOptionByCode('bundle_selection_ids');

                $selectionsCollection = $typeInstance->getSelectionsByIds(
                    unserialize($selectionsQuoteItemOption->getValue()),
                    $product
                );

                $bundleOptions = $optionsCollection->appendSelections($selectionsCollection, true);
                foreach ($bundleOptions as $bundleOption) {
                    if ($bundleOption->getSelections()) {
                        $option = array(
                            'label' => $bundleOption->getTitle(),
                            'value' => array()
                        );

                        $bundleSelections = $bundleOption->getSelections();

                        foreach ($bundleSelections as $bundleSelection) {
                            $data = '';
                            $qty = $this->getSelectionQty($product, $bundleSelection->getSelectionId()) * 1;

                            if($helper->isPreOrder($bundleSelection->getId())) {
                                $data = "<b style='float:right;'>Preorder</b>";
                            } 

                            $price = $this->getSelectionFinalPrice($item, $bundleSelection);
                            $percent_accept=$helper->getPreorderPercent();
                            $_product = Mage::getModel('catalog/product')->loadByAttribute('name',$bundleSelection->getName());
                            
                            if($helper->isPreOrder($_product->getId())) {
                                $price=($price*$percent_accept)/100; 
                            }
                            if ($qty) {
                                $option['value'][] = $qty . ' x ' . $this->escapeHtml($bundleSelection->getName())
                                    . ' ' . Mage::helper('core')->currency(
                                        $price
                                    ).$data;
                            }
                        }
                        if ($option['value']) {
                            $options[] = $option;
                        }
                    }
                }
            }
            return $options;
        }
       
        public function getOptions(Mage_Catalog_Model_Product_Configuration_Item_Interface $item) {
            return array_merge(
                $this->getBundleOptions($item),
                Mage::helper('catalog/product_configuration')->getCustomOptions($item)
            );
        }
    }