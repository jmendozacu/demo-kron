<?php

    class Webkul_Preorder_Model_CatalogInventoryObserver extends Mage_CatalogInventory_Model_Observer {
        
        public function checkQuoteItemQty($observer) {
            $quoteItem = $observer->getEvent()->getItem();
            /* @var $quoteItem Mage_Sales_Model_Quote_Item */
            if (!$quoteItem || !$quoteItem->getProductId() || !$quoteItem->getQuote()
                || $quoteItem->getQuote()->getIsSuperMode()) {
                return $this;
            }

            /**
            * Get Qty
            */
            $qty = $quoteItem->getQty();
            $stockItem = $quoteItem->getProduct()->getStockItem();
            $parentStockItem = false;
            if ($quoteItem->getParentItem()) {
                $parentStockItem = $quoteItem->getParentItem()->getProduct()->getStockItem();
            }

            /**
             * Check item for options
             */
            $options = $quoteItem->getQtyOptions();
            if ($options && $qty > 0) {
                $qty = $quoteItem->getProduct()->getTypeInstance(true)->prepareQuoteItemQty($qty, $quoteItem->getProduct());
                $quoteItem->setData('qty', $qty);

                if ($stockItem) {
                    $result = $stockItem->checkQtyIncrements($qty);
                    if ($result->getHasError()) {
                        $quoteItem->addErrorInfo(
                            'cataloginventory',
                            Mage_CatalogInventory_Helper_Data::ERROR_QTY_INCREMENTS,
                            $result->getMessage()
                        );

                        $quoteItem->getQuote()->addErrorInfo(
                            $result->getQuoteMessageIndex(),
                            'cataloginventory',
                            Mage_CatalogInventory_Helper_Data::ERROR_QTY_INCREMENTS,
                            $result->getQuoteMessage()
                        );
                    } else {
                        // Delete error from item and its quote, if it was set due to qty problems
                        $this->_removeErrorsFromQuoteAndItem(
                            $quoteItem,
                            Mage_CatalogInventory_Helper_Data::ERROR_QTY_INCREMENTS
                        );
                    }
                }

                $quoteItemHasErrors = false;
                foreach ($options as $option) {
                    $optionValue = $option->getValue();
                    /* @var $option Mage_Sales_Model_Quote_Item_Option */
                    $optionQty = $qty * $optionValue;
                    $increaseOptionQty = ($quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty) * $optionValue;

                    $stockItem = $option->getProduct()->getStockItem();

                    if ($quoteItem->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
                        $stockItem->setProductName($quoteItem->getName());
                    }

                    /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
                    if (!$stockItem instanceof Mage_CatalogInventory_Model_Stock_Item) {
                        Mage::throwException(
                            Mage::helper('cataloginventory')->__('The stock item for Product in option is not valid.')
                        );
                    }

                    /**
                     * define that stock item is child for composite product
                     */
                    $stockItem->setIsChildItem(true);
                    /**
                     * don't check qty increments value for option product
                     */
                    $stockItem->setSuppressCheckQtyIncrements(true);

                    $qtyForCheck = $this->_getQuoteItemQtyForCheck(
                        $option->getProduct()->getId(),
                        $quoteItem->getId(),
                        $increaseOptionQty
                    );

                    $result = $stockItem->checkQuoteItemQty($optionQty, $qtyForCheck, $optionValue);

                    if (!is_null($result->getItemIsQtyDecimal())) {
                        $option->setIsQtyDecimal($result->getItemIsQtyDecimal());
                    }

                    if ($result->getHasQtyOptionUpdate()) {
                        $option->setHasQtyOptionUpdate(true);
                        $quoteItem->updateQtyOption($option, $result->getOrigQty());
                        $option->setValue($result->getOrigQty());
                        /**
                         * if option's qty was updates we also need to update quote item qty
                         */
                        $quoteItem->setData('qty', intval($qty));
                    }
                    if (!is_null($result->getMessage())) {
                        $option->setMessage($result->getMessage());
                        $quoteItem->setMessage($result->getMessage());
                    }
                    if (!is_null($result->getItemBackorders())) {
                        $option->setBackorders($result->getItemBackorders());
                    }

                    if ($result->getHasError()) {
                        $option->setHasError(true);
                        $quoteItemHasErrors = true;

                        $quoteItem->addErrorInfo(
                            'cataloginventory',
                            Mage_CatalogInventory_Helper_Data::ERROR_QTY,
                            $result->getMessage()
                        );

                        $quoteItem->getQuote()->addErrorInfo(
                            $result->getQuoteMessageIndex(),
                            'cataloginventory',
                            Mage_CatalogInventory_Helper_Data::ERROR_QTY,
                            $result->getQuoteMessage()
                        );
                    } elseif (!$quoteItemHasErrors) {
                        // Delete error from item and its quote, if it was set due to qty lack
                        $this->_removeErrorsFromQuoteAndItem($quoteItem, Mage_CatalogInventory_Helper_Data::ERROR_QTY);
                    }

                    $stockItem->unsIsChildItem();
                }
            } else {
                /* @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
                if (!$stockItem instanceof Mage_CatalogInventory_Model_Stock_Item) {
                    Mage::throwException(Mage::helper('cataloginventory')->__('The stock item for Product is not valid.'));
                }

                /**
                 * When we work with subitem (as subproduct of bundle or configurable product)
                 */
                if ($quoteItem->getParentItem()) {
                    $rowQty = $quoteItem->getParentItem()->getQty() * $qty;
                    /**
                     * we are using 0 because original qty was processed
                     */
                    $qtyForCheck = $this->_getQuoteItemQtyForCheck(
                        $quoteItem->getProduct()->getId(),
                        $quoteItem->getId(),
                        0
                    );
                } else {
                    $increaseQty = $quoteItem->getQtyToAdd() ? $quoteItem->getQtyToAdd() : $qty;
                    $rowQty = $qty;
                    $qtyForCheck = $this->_getQuoteItemQtyForCheck(
                        $quoteItem->getProduct()->getId(),
                        $quoteItem->getId(),
                        $increaseQty
                    );
                }

                $productTypeCustomOption = $quoteItem->getProduct()->getCustomOption('product_type');
                if (!is_null($productTypeCustomOption)) {
                    // Check if product related to current item is a part of grouped product
                    if ($productTypeCustomOption->getValue() == Mage_Catalog_Model_Product_Type_Grouped::TYPE_CODE) {
                        $stockItem->setProductName($quoteItem->getProduct()->getName());
                        $stockItem->setIsChildItem(true);
                    }
                }

                $result = $stockItem->checkQuoteItemQty($rowQty, $qtyForCheck, $qty);

                if ($stockItem->hasIsChildItem()) {
                    $stockItem->unsIsChildItem();
                }

                if (!is_null($result->getItemIsQtyDecimal())) {
                    $quoteItem->setIsQtyDecimal($result->getItemIsQtyDecimal());
                    if ($quoteItem->getParentItem()) {
                        $quoteItem->getParentItem()->setIsQtyDecimal($result->getItemIsQtyDecimal());
                    }
                }

                /**
                 * Just base (parent) item qty can be changed
                 * qty of child products are declared just during add process
                 * exception for updating also managed by product type
                 */
                if ($result->getHasQtyOptionUpdate()
                    && (!$quoteItem->getParentItem()
                        || $quoteItem->getParentItem()->getProduct()->getTypeInstance(true)
                            ->getForceChildItemQtyChanges($quoteItem->getParentItem()->getProduct())
                    )
                ) {
                    $quoteItem->setData('qty', $result->getOrigQty());
                }

                if (!is_null($result->getItemUseOldQty())) {
                    $quoteItem->setUseOldQty($result->getItemUseOldQty());
                }
                if (!is_null($result->getMessage())) {
                    $quoteItem->setMessage($result->getMessage());
                }

                if (!is_null($result->getItemBackorders())) {
                    $quoteItem->setBackorders($result->getItemBackorders());
                }

                if ($result->getHasError()) {
                    $quoteItem->addErrorInfo(
                        'cataloginventory',
                        Mage_CatalogInventory_Helper_Data::ERROR_QTY,
                        $result->getMessage()
                    );

                    $quoteItem->getQuote()->addErrorInfo(
                        $result->getQuoteMessageIndex(),
                        'cataloginventory',
                        Mage_CatalogInventory_Helper_Data::ERROR_QTY,
                        $result->getQuoteMessage()
                    );
                } else {
                    // Delete error from item and its quote, if it was set due to qty lack
                    $this->_removeErrorsFromQuoteAndItem($quoteItem, Mage_CatalogInventory_Helper_Data::ERROR_QTY);
                }
            }
            return $this;
        }
    }