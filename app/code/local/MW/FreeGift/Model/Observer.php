<?php
class MW_FreeGift_Model_Observer extends Mage_Core_Model_Abstract
{
    protected $_validator;

    protected function _getMaxFreeItem()
    {
        return Mage::getSingleton('core/session')->getCountFreeGift();
    }

    public function prepareLayoutBefore(Varien_Event_Observer $observer)
    {
        /* @var $block Mage_Page_Block_Html_Head */
        $block = $observer->getEvent()->getBlock();

        if ("head" == $block->getNameInLayout()) {
            foreach (Mage::helper('freegift/jquery')->getFiles() as $file) {
                $block->addJs(Mage::helper('freegift/jquery')->getJQueryPath($file));
            }
        }

        return $this;
    }

    protected function _getNumberOfAddedFreeItems()
    {
        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
        $countFreeItem = 0;
        foreach ($items as $item) {
            if ($item->getParentItem())
                continue;
            $params = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
            if (isset($params['freegift']) && $params['freegift']) {
                $countFreeItem++;
            }
        }
        return $countFreeItem;
    }

    protected function _initProduct($productId)
    {
        if ($productId) {
            $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }


    protected function getAplliedRule($request)
    {
        if (isset($request['apllied_rule']) && $request['apllied_rule'])
            return Mage::getModel('freegift/salesrule')->load($request['apllied_rule']);
        return false;
    }

    protected function getCatalogAplliedRule($request)
    {
        if (isset($request['apllied_catalog_rules']) && $request['apllied_catalog_rules'])
            return Mage::getModel('freegift/rule')->load($request['apllied_catalog_rules']);
        return false;
    }

    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    protected function _getFreeGiftItemByGiftKey($key, $quote)
    {
        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        foreach ($quote->getAllItems() as $item) {
            $params = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
            if (isset($params['freegift_key']) && ($params['freegift_key'] == $key))
                return $item;
        }
        return false;
    }

    public function getQuoteItemByGiftKey($key)
    {
        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        foreach ($items as $item) {
            $params = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
            if (isset($params['freegift_parent_key']) && ($params['freegift_parent_key'] == $key))
                return $item;
        }
        return false;
    }

    public function getQuoteItemByGiftItemId($item_id)
    {
        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        foreach ($items as $item) {
            if ($item_id == $item->getItemId())
                return $item;
        }
        return false;
    }

    public function checkoutCartProductAddAfter($argv)
    {
        /* While going to the cart page, this function will go through before */
        if (!Mage::getStoreConfig('freegift/config/enabled'))
            return false;

        $_product = $argv->getProduct();
        $quote_item = $argv->getQuoteItem();
        $infoRequest = unserialize($quote_item->getOptionByCode('info_buyRequest')->getValue());

        $freegiftProduct = Mage::getModel('freegift/product')->init($_product);
        $freeproducts = $freegiftProduct->getFreeGifts();
        $cart = Mage::getSingleton('checkout/cart');
        /* Free items with coupon code */
        if (isset($infoRequest['freegift_with_code']) && $infoRequest['freegift_with_code']) {
            $this->setDefaultQuoteItem($quote_item, 1);

            $infoRequest['option'] = serialize(array(
                'label' => Mage::helper('freegift')->__('Free Gift with coupon Code'),
                'value' => $infoRequest['freegift_coupon_code']
            ));
            $quote_item->getOptionByCode('info_buyRequest')->setValue(serialize($infoRequest));

            /*$_options = array(
                1 => array(
                    'label' => Mage::helper('freegift')->__('Free Gift with coupon Code'),
                    'value' => $infoRequest['freegift_coupon_code'],
                    'print_value' => $infoRequest['freegift_coupon_code'],
                    'option_type' => 'text',
                    'custom_view' => true
                )
            );
            $options = array(
                'code' => 'additional_options',
                'value' => serialize($_options)
            );
            $quote_item->addOption($options);*/
        }
        /* Shopping cart Free Gift */

        if (isset($infoRequest['freegift']) && $infoRequest['freegift']) {
            return;
            $this->addProductWithRule($infoRequest, $_product, $quote_item);
        }

        /* Catalog Free Gift */

        if (isset($infoRequest['free_catalog_gift']) && $infoRequest['free_catalog_gift']) {
            return;
            /* Add custom option to catalog gift*/
            $parentGiftItem = $this->_getFreeGiftItemByGiftKey($infoRequest['freegift_parent_key'], $quote_item->getQuote());
            if ($parentGiftItem == null) return false;
            $_infoRequest = unserialize($parentGiftItem->getOptionByCode('info_buyRequest')->getValue());

            foreach (unserialize($_infoRequest['apllied_catalog_rules']) as $ruleId) {
                $tmpRule = Mage::getModel('freegift/rule')->load($ruleId);
                if ($tmpRule->getData('discount_qty') && ($tmpRule->getData('discount_qty') < $tmpRule->getData('times_used'))) {
                    continue;
                }
                if (in_array($_product->getId(), explode(',', $tmpRule->getData('gift_product_ids')))) {
                    $infoRequest['option'] = serialize(array(
                        'label' => Mage::helper('freegift')->__('Free Gift'),
                        'value' => $tmpRule->getDescription()
                    ));
                    $quote_item->getOptionByCode('info_buyRequest')->setValue(serialize($infoRequest));
                    $_options = array(
                        1 => array(
                            'label' => Mage::helper('freegift')->__('Free Gift'),
                            'value' => $tmpRule->getDescription(),
                            'print_value' => $tmpRule->getDescription(),
                            'option_type' => 'text',
                            'custom_view' => true
                        )
                    );
                    $options = array(
                        'code' => 'additional_options',
                        'value' => serialize($_options)
                    );

                    $this->setDefaultQuoteItem($quote_item);

                    $quote_item->addOption($options);
                    break;
                }

            }
        }
        if ($freeproducts && sizeof($freeproducts)) {
            //Get Catalog Rules
            $applied_rule_ids = $freegiftProduct->getAplliedRuleIds();
            $this->_getSession()->getQuote()->collectTotals();

            if ((isset($infoRequest['free_catalog_gift']) && $infoRequest['free_catalog_gift']) || (isset($infoRequest['freegift']) && $infoRequest['freegift']) || (isset($infoRequest['freegift_with_code']) && $infoRequest['freegift_with_code'])) {
                return false;
            }

            if (!isset($infoRequest['freegift_key'])) {
                $randKey = md5(rand(1111, 9999));
                $infoRequest['freegift_key'] = $randKey;
                $infoRequest['apllied_catalog_rules'] = serialize($applied_rule_ids);
            } else {
                $randKey = $infoRequest['freegift_key'];
            }
            $quote_item->getOptionByCode('info_buyRequest')->setValue(serialize($infoRequest));
            $quote_item->getQuote()->save();
            $cart->save();
            /* if product type if bundle && configurable then stop add gift */

            $now = Mage::getModel('core/date')->date('Y-m-d');
            foreach ($applied_rule_ids as $ruleId) {
                $tmpRule = Mage::getModel('freegift/rule')->load($ruleId);

                if (!$tmpRule->getIsActive()) {
                    continue;
                }
                if ($tmpRule->getData('discount_qty') && ($tmpRule->getData('discount_qty') < $tmpRule->getData('times_used'))) {
                    continue;
                }

                if ((!$tmpRule->getFromDate() || $now >= $tmpRule->getFromDate()) && (!$tmpRule->getToDate() || $now <= $tmpRule->getToDate())) {
                    $custom_cdn = unserialize($tmpRule->getconditionCustomized());
                    $giftProductIds = explode(',', $tmpRule->getGiftProductIds());
                    if(isset($custom_cdn['buy_x_get_y'])){
                        if($quote_item->getQty() < $custom_cdn['buy_x_get_y']['bx']){
                            continue;
                        }
                    }
                    foreach ($giftProductIds as $productId) {
                        $product = $this->_initProduct($productId);
                        $_product = Mage::getModel('catalog/product')->load($productId);

                        if ($product->getTypeId() == 'configurable') {
                            continue;
                            //continue; Becase, this product will add to gift list, dont need auto add cart

                            $attrs = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);

                            $super_attributes = array();
                            foreach ($attrs as $kp => $attr) {
                                $count = 0;
                                foreach ($attr['values'] as $kc => $spa) {
                                    //Loop attribute and get first option
                                    if ($count > 0) break;
                                    $super_attributes[$attr['attribute_id']] = $spa['value_index'];
                                    $childProduct = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($super_attributes, $product);
                                    $childStockQty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct)->getQty();

                                    if (!$childProduct->isSalable()) {
                                        continue;
                                    } else {
                                        $count++;
                                    }
                                }
                            }
                        }
                        if (!$product->isSaleable()) {
                            continue;
                        }

                        $stock_qty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQty();
                        $qty_4gift = $this->getQtyToAdded($product, array('qty' => $quote_item->getQty()), $quote_item, $stock_qty);

                        $product->addCustomOption('free_catalog_gift', 1);
                        $product->addCustomOption('freegift_parent_key', $randKey);
                        $product->addCustomOption(base64_encode(microtime()), serialize(array(time())));
                        $product->setPrice(0);

                        $request = array(
                            'uenc' => $infoRequest['uenc'],
                            'product' => $product->getId(),
                            'qty' => $qty_4gift,
                            'free_catalog_gift' => 1,
                            'freegift_parent_key' => $randKey,
                            'applied_catalog_rule'  => $ruleId,
                            'text_gift' => array(
                                'label' => 'Free Gift',
                                'value' => $tmpRule->getName()
                            )
                        );

                        if ($product->getTypeId() == 'configurable') {
                            $request['super_attribute'] = $super_attributes;
                        }
                        if ($qty_4gift > 0) {
                            $cart->addProduct($product, $request);
                            $cart->save();
                            Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
                            //$this->_getSession()->addSuccess(Mage::helper('freegift')->__('%s was automaticly added to your shopping cart', $product->getName()));
                        }
                    }
                }
            }

            $_infoRequest = unserialize($infoRequest['apllied_catalog_rules']);

            $this->_getSession()->getQuote()->setTotalsCollectedFlag(false);
            $this->_getSession()->getQuote()->collectTotals();
            $this->_getSession()->getQuote()->setTotalsCollectedFlag(false);

        }
    }

    public function itemCollectionProductsAfterLoad($argvs)
    {
        /**
         * Added by ANH TO
         */
        /** Event Update checkout cart and page checkout cart running here */
        if (!Mage::getStoreConfig('freegift/config/enabled'))
            return false;
        $_cart = $argvs->getCart();
        $quote = $_cart->getQuote();
        $items = $quote->getAllVisibleItems();
        $loop = true;
        $qtyProductsUsed = array();
        $now = Mage::getModel('core/date')->date('Y-m-d');

        foreach ($items as $item) {
            $params = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
            /** $quote_parent_item_id: if the current item have parent item ( configurable, bundle ) then query parent item to get original quantity */
            $quote_parent_item_id = 0;
            if ($item->getParentItem()) {
                if (isset($params['freegift_parent_key'])) {
                    if ($quote_parent_item_id == 0) {
                        foreach ($items as $_item) {
                            $_params = unserialize($_item->getOptionByCode('info_buyRequest')->getValue());
                            if ($params['freegift_parent_key'] == $_params['freegift_key']) {
                                $quote_parent_item_id = $_item->getId();
                                break;
                            }
                        }
                    }
                }
            } else {
                $quote_parent_item_id = $item->getItemId();
            }
            if (!isset($params['freegift_key'])) {
                //If item is gift product
                if (!isset($params['free_catalog_gift']) || !isset($params['freegift']) || !isset($params['freegift_with_code'])) {
                    foreach ($item->getChildren() as $_item) {
                        $_params = unserialize($_item->getOptionByCode('info_buyRequest')->getValue());
                        if (isset($_params['freegift_key'])) {
                            //Re-save infoBuy_request
                            $collection = Mage::getModel('sales/quote_item_option')->getCollection();
                            $con = Mage::getModel('core/resource')->getConnection('core_write');
                            $sql = "UPDATE {$collection->getTable('sales/quote_item_option')} SET value= '" . serialize($_params) . "' WHERE product_id={$_params['product']} AND code = 'info_buyRequest'";
                            $con->query($sql);
                            break;
                        }
                    }
                }
            }


            $_quotes_parent = Mage::getModel('sales/quote_item')->load($quote_parent_item_id);

            $product = $this->_initProduct($item->getProductId());
            $_product = Mage::getModel('catalog/product')->load($item->getProductId());

            $stock_qty = (int)$_quotes_parent->getQty();

            if ($_product->getTypeId() == 'configurable') {
                /** Attributes on view product (frontend) */
                $childProduct = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($params['super_attribute'], $product);
                $childStockQty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct)->getQty();
                $managechildStock = $childProduct->getStockItem()->getManageStock();
                $stockQty = $childStockQty;
            } else if ($_product->getTypeId() == 'simple') {
                $stockQty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($item->getProduct())->getQty();
                $manageStock = $_product->getStockItem()->getManageStock();
            } else {
                $stockQty = $item->getQty();
            }

            /** check if product is Catalog Rule */
            if (isset($params['free_catalog_gift']) && $params['free_catalog_gift']) {
                /** Get main product from this item */
                $parent_key_item = $this->getParentQuoteItemByKey($params);

                if (!$parent_key_item) {
                    $quote->removeItem($item->getId());
                    $_cart->removeItem($item->getId())->save();
                    continue;
                }
                $infoBuyRequest = unserialize($parent_key_item->getOptionByCode('info_buyRequest')->getValue());
                $appliedCatalogRule = unserialize($infoBuyRequest['apllied_catalog_rules']);
                $rule = Mage::getModel('freegift/rule')->load($params['applied_catalog_rule']);
                /** get condition buy X get Y */
                $custom_cdn = unserialize($rule->getconditionCustomized());

                $first_rem = false;

                if (!$rule->getIsActive()) {
                    $first_rem = true;
                }
                if ($rule->getData('discount_qty') && ($rule->getData('discount_qty') < $rule->getData('times_used'))) {
                    $first_rem = true;
                }

                if ($first_rem) {
                    $quote->removeItem($item->getId());
                    $item->save();
                    continue;
                }

                /* check quantity */
                $qty_4gift = 0;
                if ($product->getTypeId() == 'configurable') {
                    if ((int)$_quotes_parent->getQty() > $childStockQty) {
                        $qty_4gift = $childStockQty;
                    } else {
                        $qty_4gift = (int)$_quotes_parent->getQty();
                    }
                } else if ($product->getTypeId() == 'simple') {
                    $qty_4gift = $this->getFinalQty($parent_key_item, $item, $stockQty);
                } else {
                    $qty_4gift = $this->getFinalQty($_quotes_parent, $item, $stockQty);
                }

                if ($qty_4gift > 0) {
                    if (isset($custom_cdn['buy_x_get_y'])) {
                        if ($parent_key_item->getQty() < $custom_cdn['buy_x_get_y']['bx']) {
                            $quote->removeItem($item->getId());
                            $item->save();
                            continue;
                        }
                    }
                    if (isset($custom_cdn['buy_x_get_y']) && $custom_cdn['buy_x_get_y']['bx'] == 1 && $custom_cdn['buy_x_get_y']['gy'] == 1) {
                        $item->setQty($qty_4gift);
                        $item->save();
                    } else if ($custom_cdn['buy_x_get_y']['bx'] < $custom_cdn['buy_x_get_y']['gy']) {
                        $new_qty_4gift = floor($parent_key_item->getQty() / $custom_cdn['buy_x_get_y']['bx']) * $custom_cdn['buy_x_get_y']['gy'];
                        $item->setQty(($new_qty_4gift > $stockQty) ? $stockQty : $new_qty_4gift);
                    } else {
                        $qty_4gift = floor($parent_key_item->getQty() / $custom_cdn['buy_x_get_y']['bx']) * $custom_cdn['buy_x_get_y']['gy'];

                        $item->setQty(($qty_4gift > $stockQty) ? $stockQty : $qty_4gift);
                    }
                    $item->setPrice(0);
                    $item->setRowTotal(0);
                    $item->setBaseRowTotal(0);
                    $item->save();
                } else {
                    $quote->removeItem($item->getId());
                    $item->save();
                }
            }
        }
    }
    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    public function checkoutCartEmptyAfter($argvs)
    {
        Mage::getSingleton('core/session')->unsFlagRule();
        Mage::getSingleton('core/session')->unsFlagCoupon();
    }

    protected function getFinalQty($parent_item, $item, $stockQty)
    {
        if ($parent_item->getProductId() == $item->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($parent_item->getProductId());
            $manageStock = $product->getStockItem()->getManageStock();
            /** If Parent quote product is the same children quote product */
            /* if new quantity greater than stock qty */
            if ($manageStock) {
                if ((int)$parent_item->getQty() >= $stockQty) {
                    $qty_4gift = $stockQty - $parent_item->getQty();
                } else if (2 * $parent_item->getQty() > $stockQty) {
                    $qty_4gift = $stockQty - $parent_item->getQty();
                } else {
                    $qty_4gift = (int)$parent_item->getQty();
                }
            } else {
                $qty_4gift = (int)$parent_item->getQty();
            }
        } else {
            if ($manageStock) {
                /* if new quantity greater than stock qty */
                if ((int)$parent_item->getQty() > $stockQty) {
                    $qty_4gift = $stockQty;
                } else {
                    $qty_4gift = (int)$parent_item->getQty();
                }
            } else {
                $qty_4gift = (int)$parent_item->getQty();
            }
        }
        return $qty_4gift;
    }

    protected function getParentQuoteItemByKey($params)
    {
        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();
        foreach ($items as $item) {
            $info_buyRequest = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
            if (!isset($info_buyRequest['freegift_key'])) continue;
            if (isset($params['freegift_parent_key'])) {
                if ($params['freegift_parent_key'] == $info_buyRequest['freegift_key']) {
                    return $item;
                }
            }
        }
        return false;
    }

    public function abstractToHtmlBefore($observer)
    {
        /** @var $_block Mage_Core_Block_Abstract */
        /*Get block instance*/
        $_block = $observer->getBlock();
        /*get Block type*/
        $_type = $_block->getType();
        /*Check block type*/
        if ($_type == 'catalog/product_price') {
            if (Mage::getStoreConfig('freegift/config/enabled')) {
                /*Clone block instance*/
                $_child = clone $_block;
                /*set another type for block*/
                $_child->setType('freegift/labelgift_category');
                /*set child for block*/
                $_block->setChild('child', $_child);
                /*set our template*/
                //$_block->setTemplate('mw_freegift/labelgift/category.phtml')->setProduct($_block->getProduct());
            }
        }
    }

    public function checkout_cart_add_product_complete($argvs)
    {
        $params = $argvs->getParams();
        $product = $argvs->getProduct();
        $cart = $argvs->getCart();

        return $this->checkout_cart_add_product($params, $product, $cart);
    }

    public function checkout_cart_add_product($params, $product, $cart)
    {
        if (isset($params['free_catalog_gift'])) {
            $flag_update = true;
            if (isset($params['super_attribute'])) {
                foreach ($params['super_attribute'] as $k => $attr) {
                    if (empty($attr)) {
                        $flag_update = false;
                    }
                }
            }
            if (isset($params['options'])) {
                foreach ($params['options'] as $k => $attr) {
                    if (empty($attr)) {
                        $flag_update = false;
                    }
                }
            }

            if (!isset($params['upd'])) {
                $block_product = Mage::app()->getLayout()->getBlockSingleton('freegift/product');
                $missingGiftProducts = $block_product->getFreeGiftCatalogProduct();

                $quote_parent_item = $this->getQuoteItemByGiftItemId($params['free_catalog_gift']);
                $optionParentCollection = Mage::getModel('sales/quote_item_option')
                    ->getCollection()
                    ->addFieldToFilter('item_id', $params['free_catalog_gift']);

                foreach ($optionParentCollection as $opt) {
                    if ($opt->getCode() == 'info_buyRequest') {
                        $infoRequest = unserialize($opt->getValue());
                        break;
                    }
                }
            }

            if ($flag_update === false) {
                echo json_encode(array('message' => 'Empty options.', 'error' => 1, 'action' => 'load_in_page', 'item_id' => ''));
                exit;
            }
            if (isset($params['upd'])) {
                if ($flag_update) {
                    $quote_item = $this->getQuoteItemByGiftItemId($params['item_id']);
                    if (isset($params['options'])) {
                        foreach ($params['options'] as $optId => $opt) {
                            $quote_item->getOptionByCode('option_' . $optId)->setValue($opt);
                        }
                    }
                    $quote_item->getOptionByCode('attributes')->setValue(serialize($params['super_attribute']));
                    $quote_item->getQuote()->save();
                    $cart->save();

                    $block_cart = Mage::app()->getLayout()->createBlock('checkout/cart');
                    echo json_encode(array(
                        'message'   => '',
                        'error'     => 0,
                        'upd'       => 1,
                        'item_id'   => $quote_item->getItemId(),
                        'item_html' => $block_cart->addItemRender($quote_item->getProduct()->getTypeId(), 'checkout/cart_item_renderer', 'mw_freegift/checkout/cart/item/default.phtml')->getItemHtml($quote_item)
                    ));
                    return;
                }
            }

            $params['qty'] = $quote_parent_item->getQty();;

            if ($quote_parent_item->getItemId()) {
                $_product = Mage::getModel('catalog/product')->load($quote_parent_item->getProductId());
                $stock_qty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQty();
                $infoRequestParent = unserialize($quote_parent_item->getOptionByCode('info_buyRequest')->getValue());
                $catalogRules = unserialize($infoRequestParent['apllied_catalog_rules']);

                if(!in_array($params['applied_catalog_rule'], $catalogRules)){
                    return false;
                }
                $rule = Mage::getModel('freegift/rule')->load($params['applied_catalog_rule']);

                if (isset($missingGiftProducts[$params['free_catalog_gift']]) && in_array($params['product'], $missingGiftProducts[$params['free_catalog_gift']][$params['applied_catalog_rule']])) {
                    $product->addCustomOption('free_catalog_gift', 1);
                    $product->addCustomOption('freegift_parent_key', $infoRequest['freegift_key']);
                    $product->addCustomOption(base64_encode(microtime()), serialize(array(time())));
                    $product->setPrice(0);
                    $qty_4gift = $this->getQtyToAdded($product, $params, $quote_parent_item, $stock_qty);
                    unset($params['freegift']);

                    if ($qty_4gift > 0) {
                        $params['qty'] = $qty_4gift;
                        $params['free_catalog_gift'] = 1;
                        $params['freegift_parent_key'] = $infoRequest['freegift_key'];
                        $params['applied_catalog_rule'] = $params['applied_catalog_rule'];
                        $params['text_gift'] = array(
                            'label' => 'Free Gift',
                            'value' => $rule->getName()
                        );

                        $cart->addProduct($product, $params);
                        /** Bug of 1.8, cant check quantity based the options */
                        $cart->save();

                        if (isset($params['ajax_gift']) && $params['ajax_gift']) {
                            $last_item = $this->getLastItemAdded();
                            $block_cart = Mage::app()->getLayout()->createBlock('checkout/cart');
                            $block_freegift = Mage::app()->getLayout()->createBlock('freegift/product');
                            $quote_item = $this->getQuoteItemByGiftItemId($last_item['item_id']);
                            echo json_encode(array(
                                'message'       => '',
                                'error'         => 0,
                                'item_id'       => $quote_item->getItemId(),
                                'item_html'     => $block_cart->addItemRender($quote_item->getProduct()->getTypeId(), 'checkout/cart_item_renderer', 'mw_freegift/checkout/cart/item/default.phtml')->getItemHtml($quote_item),
                                'freegift'      => $block_freegift->toHtml(),
                            ));
                            exit;
                        }
                        Mage::getSingleton('checkout/session')->setCartWasUpdated(false);
                    } else {
                        echo json_encode(array('message' => 'Out of stock.', 'error' => 1));
                        exit;
                    }
                }
            }
        }

        if ((isset($params['freegift']) && $params['freegift']) || isset($params['freegift_with_code']) && $params['freegift_with_code']) {
            if (isset($params['product']) && is_numeric($params['product'])) {
                $flag_update = true;
                if (isset($params['super_attribute'])) {
                    foreach ($params['super_attribute'] as $k => $attr) {
                        if (empty($attr)) {
                            $flag_update = false;
                        }
                    }
                }
                if (isset($params['options'])) {
                    foreach ($params['options'] as $k => $attr) {
                        if (empty($attr)) {
                            $flag_update = false;
                        }
                    }
                }
                if ($flag_update === false) {
                    echo json_encode(array('message' => 'Empty options.', 'error' => 1, 'action' => 'load_in_page', 'item_id' => ''));
                    exit;
                }

                if (isset($params['upd'])) {
                    $quote_item = $this->getQuoteItemByGiftItemId($params['item_id']);
                    if (isset($params['options'])) {
                        foreach ($params['options'] as $optId => $opt) {
                            $quote_item->getOptionByCode('option_' . $optId)->setValue($opt);
                        }
                    }
                    $quote_item->getOptionByCode('attributes')->setValue(serialize($params['super_attribute']));
                    $quote_item->getQuote()->save();
                    $cart->save();
                    $block_cart = Mage::app()->getLayout()->createBlock('checkout/cart');
                    echo json_encode(array(
                        'message'   => '',
                        'error'     => 0,
                        'upd'       => 1,
                        'item_id'   => $quote_item->getItemId(),
                        'item_html' => $block_cart->addItemRender($quote_item->getProduct()->getTypeId(), 'checkout/cart_item_renderer', 'mw_freegift/checkout/cart/item/default.phtml')->getItemHtml($quote_item)
                    ));
                    return;
                }

                $params['qty'] = (!isset($params['qty'])) ? 1 : $params['qty'];
                $stock_qty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();

                $qty_4gift = $this->getQtyToAdded($product, $params, null, $stock_qty);
                if (Mage::app()->getFrontController()->getRequest()->getParam('apllied_rule')) {
                    $rule = Mage::getModel('freegift/salesrule')->load((int)Mage::app()->getFrontController()->getRequest()->getParam('apllied_rule'));
                    $params['text_gift'] = array(
                        'label' => (isset($params['freegift_coupon_code']) || isset($params['freegift_with_code']) ? 'Free Gift with coupon' : 'Free Gift'),
                        'value' => $rule->getName()
                    );
                }

                $product->addCustomOption((isset($params['freegift_with_code']) ? 'freegift_with_code' : 'freegift'), 1);
                $product->addCustomOption(base64_encode(microtime()), serialize(array(time())));
                $product->setPrice(0);

                $quote_item = $cart->addProduct($product, $params);
                $cart->save();
                $last_item = $cart->getItems()->addFieldToFilter('product_id', $params['product'])->getLastItem();
                if ($last_item->getParentItem()) {
                    $last_item_insert_id = $last_item->getParentItemId();
                } else {
                    $last_item_insert_id = $last_item->getItemId();
                }

                $last_item_insert = Mage::getModel('sales/quote_item')->setStoreId(Mage::app()->getStore()->getId())->load($last_item_insert_id);

                foreach ($cart->getQuote()->getAllVisibleItems() as $item) {
                    if ($item->getItemId() != $last_item_insert_id) continue;
                    $quote_item = $item;
                    break;
                }

                $this->addProductWithRule($params, $product, $quote_item);
                if (isset($params['ajax_gift']) && $params['ajax_gift']) {
                    $block_cart = Mage::app()->getLayout()->createBlock('checkout/cart');
                    $block_totals = Mage::app()->getLayout()->createBlock('checkout/cart_totals');
                    $block_freegift = Mage::app()->getLayout()->createBlock('freegift/product');
                    echo json_encode(array(
                        'message'       => '',
                        'error'         => 0,
                        'item_id'       => $quote_item->getItemId(),
                        'item_html'     => $block_cart->addItemRender($quote_item->getProduct()->getTypeId(), 'checkout/cart_item_renderer', 'mw_freegift/checkout/cart/item/default.phtml')->getItemHtml($quote_item),
                        'freegift'      => $block_freegift->toHtml(),
                    ));
                    exit;
                }
                Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
                exit;
            }
        }

        return false;
    }

    public function sessionUpdateCart()
    {
        Mage::getSingleton('checkout/session')->getQuote()->setTotalsCollectedFlag(false);
        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
    }

    public function catalog_product_type_prepare_full_options($argvs)
    {
        $transport = $argvs->getTransport();
        $buyRequest = $argvs->getBuyRequest();
        $product = $argvs->getProduct();
        if ($buyRequest->getFreegift())
            $transport->options['freegift'] = '1';
    }

    public function processFrontFinalPrice($observer)
    {
        $product = $observer->getEvent()->getProduct();
        $pId = $product->getId();
        $storeId = $product->getStoreId();
        if ($product->getCustomOption('free_catalog_gift') || $product->getCustomOption('freegift')) {
            $product->setFinalPrice(0);
        }
    }

    public function sales_order_beforePlace($observer)
    {
    }

    public function sales_order_afterPlace($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $items = $order->getAllItems();

        if (!$order) {
            return $this;
        }

        Mage::getSingleton('checkout/session')->setRulegifts('');
        Mage::getSingleton('checkout/session')->setProductgiftid('');
        Mage::getSingleton('checkout/session')->setGooglePlus('');
        Mage::getSingleton('checkout/session')->setLikeFb('');
        Mage::getSingleton('checkout/session')->setShareFb('');
        Mage::getSingleton('checkout/session')->setTwitter('');

        $rule_inserted = array();
        foreach ($items as $item) {
            if ($item->getParentItem())
                continue;

            $collection = Mage::getModel('freegift/rule')->getCollection();
            $con = Mage::getModel('core/resource')->getConnection('core_write');
            //Catalog rules
            $infoRequest = $item->getProductOptionByCode('info_buyRequest');

            if (isset($infoRequest['apllied_rules']) && $infoRequest['apllied_rules']) {
                $apllied_rules = unserialize($infoRequest['apllied_rules']);
                if (sizeof($apllied_rules))
                    foreach ($apllied_rules as $rule_id) {
                        // increase time_used
                        if (!in_array($rule_id, $rule_inserted['applied_rules'])) {
                            $sql = "UPDATE {$collection->getTable('freegift/rule')} SET times_used=times_used+1 WHERE rule_id={$rule_id}";
                            $con->query($sql);
                            $rule_inserted['applied_rules'][] = $rule_id;
                        }
                    }
            }
            if (isset($infoRequest['apllied_catalog_rules']) && $infoRequest['apllied_catalog_rules']) {
                $apllied_rules = unserialize($infoRequest['apllied_catalog_rules']);
                if (sizeof($apllied_rules))
                    foreach ($apllied_rules as $rule_id) {
                        // increase time_used
                        if (!in_array($rule_id, $rule_inserted['apllied_catalog_rules'])) {
                            $sql = "UPDATE {$collection->getTable('freegift/rule')} SET times_used=times_used+1 WHERE rule_id={$rule_id}";
                            $con->query($sql);
                            $rule_inserted['apllied_catalog_rules'][] = $rule_id;
                        }
                    }
            }
            //Sales Rules
            if (!in_array($infoRequest['apllied_rule'], $rule_inserted['applied_rules'])) {
                if (isset($infoRequest['apllied_rule']) && $infoRequest['apllied_rule']) {
                    if (isset($infoRequest['apllied_rule'])):
                        $sql = "UPDATE {$collection->getTable('freegift/salesrule')} SET times_used=times_used+1 WHERE rule_id={$infoRequest['apllied_rule']}";
                        $con->query($sql);
                    endif;
                }
                $rule_inserted['applied_rules'][] = $infoRequest['apllied_rule'];
            }

            if (!in_array($infoRequest['rule_id'], $rule_inserted['applied_rules'])) {
                if (isset($infoRequest['freegift_with_code']) && $infoRequest['freegift_with_code']) {
                    if (isset($infoRequest['rule_id'])):
                        $sql = "UPDATE {$collection->getTable('freegift/salesrule')} SET times_used=times_used+1 WHERE rule_id={$infoRequest['rule_id']}";
                        $con->query($sql);
                    endif;
                }
                $rule_inserted['applied_rules'][] = $infoRequest['rule_id'];
            }
        }
    }

    public function getValidator($event)
    {
        if (!$this->_validator) {
            $this->_validator = Mage::getModel('freegift/validator')->init($event->getWebsiteId(), $event->getCustomerGroupId(), $event->getFreegiftCouponCode());
        }
        return $this->_validator;
    }

    public function freegift_quote_address_freegift_item($observer)
    {
        $this->getValidator($observer->getEvent())->process($observer->getEvent()->getItem());
    }

    public function addProductAttributes(Varien_Event_Observer $observer)
    {
        // @var Varien_Object
        $attributesTransfer = $observer->getEvent()->getAttributes();

        $attributes = Mage::getResourceModel('freegift/salesrule')->getActiveAttributes(Mage::app()->getWebsite()->getId(), Mage::getSingleton('customer/session')->getCustomer()->getGroupId());
        $result = array();
        foreach ($attributes as $attribute) {
            $result[$attribute['attribute_code']] = true;
        }
        $attributesTransfer->addData($result);
        return $this;
    }

    public function sales_quote_product_add_after(Varien_Event_Observer $observer)
    {
        if (!Mage::getStoreConfig('freegift/config/enabled'))
            return false;
        $items = $observer->getItems();
        $groups = array();
        foreach ($items as $item) {
            $infoRequest = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
            $superConfig = "";

            if (isset($infoRequest['super_product_config']))
                $superConfig = $infoRequest['super_product_config'];

            if (isset($superConfig) && is_array($superConfig)) {
                if ($superConfig['product_type'] == 'grouped') {
                    $_product = Mage::getModel('catalog/product')->load($superConfig['product_id']);
                    $freegiftProduct = Mage::getModel('freegift/product')->init($_product);
                    $freeproducts = $freegiftProduct->getFreeGifts();
                    $cart = Mage::getSingleton('checkout/cart');
                    if ($freeproducts && sizeof($freeproducts)) {
                        //Catalog Rules
                        $applied_rule_ids = $freegiftProduct->getAplliedRuleIds();
                        $this->_getSession()->getQuote()->collectTotals();
                        $randKey = md5(rand(1111, 9999));
                        if ((isset($infoRequest['free_catalog_gift']) && $infoRequest['free_catalog_gift']) || (isset($infoRequest['freegift']) && $infoRequest['freegift']) || (isset($infoRequest['freegift_with_code']) && $infoRequest['freegift_with_code']))
                            return;
                        if (!isset($infoRequest['freegift_key'])) {
                            $infoRequest['freegift_key'] = $randKey;
                            $infoRequest['apllied_rules'] = serialize($applied_rule_ids);
                        }
                        $item->getOptionByCode('info_buyRequest')->setValue(serialize($infoRequest));

                        foreach ($freeproducts as $productId) {
                            $product = $this->_initProduct($productId);
                            if ($product->getTypeId() == 'simple') {
                                $product->addCustomOption('free_catalog_gift', 1);
                                $product->addCustomOption('freegift_parent_key', $randKey);
                                $product->setPrice(0);
                                $request = array(
                                    'uenc' => $infoRequest['uenc'],
                                    'product' => $product->getId(),
                                    'qty' => $item->getQty(),
                                    'free_catalog_gift' => 1,
                                    'freegift_parent_key' => $randKey
                                );
                                $cart->addProduct($product, $request);
                                $cart->save();
                                Mage::getSingleton('checkout/session')->setCartWasUpdated(false);

                                $this->_getSession()->addSuccess(Mage::helper('freegift')->__('%s was automaticly added to your shopping cart', $product->getName()));
                            }
                        }
                    }
                }
            }
        }
        $this->_getSession()->getQuote()->setTotalsCollectedFlag(false);
        $this->_getSession()->getQuote()->collectTotals();
        $this->_getSession()->getQuote()->setTotalsCollectedFlag(false);
    }

    public function getLastItemAdded()
    {
        $cart = Mage::getSingleton('checkout/cart');
        $collection = Mage::getSingleton('sales/quote_item')->setQuote($cart->getQuote())->getCollection()->addFieldToFilter('parent_item_id', array('null' => true));
        $collection->getSelect()->order('created_at DESC')->reset(Zend_Db_Select::COLUMNS)->columns('item_id')->limit(1);
        $item = $collection->getData();
        return end($item);
    }

    public function addOptions($observer)
    {
        $ajax = Mage::app()->getFrontController()->getRequest()->getParams(); //

        $isajax = Mage::getSingleton('checkout/session')->getIsajax();
        if (!isset($isajax)) {
            Mage::getSingleton('checkout/session')->setIsajax('0');
            $isajax = 0;
        }
        if (!isset($ajax['isCart']) AND $isajax == '0')
            return;

        Mage::getSingleton('checkout/session')->setIsajax('0');

        $cart = Mage::helper('freegift')->renderOptions();
        Mage::helper('freegift')->sendResponse($cart, "", "");

    }

    public function addToCart($observer)
    {
        if (Mage::app()->getFrontController()->getRequest()->getParam('isCart') != "") {
            $product = $this->_initProduct();

            $message = Mage::getSingleton('checkout/session')->getMessages();
            $hasnotice = false;

            if ($message->getItems('notice')) {
                $hasnotice = true;

            }

            Mage::getSingleton('checkout/session')->getData('messages')->clear(); //die();

            $ajax = Mage::app()->getFrontController()->getRequest()->getParams(); //

            if ($product->getTypeId() == 'grouped' AND Mage::app()->getFrontController()->getRequest()->getParam('isCart') != null AND !isset($ajax['related_product'])) {
                Mage::getSingleton('checkout/session')->setIsajax("1");
                return;

            }
            if ($product->getHasOptions() AND $hasnotice) {
                $hasnotice = false;
                Mage::getSingleton('checkout/session')->setIsajax("1");
                Mage::getSingleton('checkout/session')->setIspage(Mage::app()->getFrontController()->getRequest()->getParam('isCart'));
                return;
            }
            if (Mage::app()->getFrontController()->getRequest()->getParam('miniwishtocart')) {
                Mage::getSingleton('checkout/session')->setIsfirst("1");
            }

            if (!Mage::app()->getFrontController()->getRequest()->getParam('isCart')) {
                Mage::helper('freegift')->_NAMEITEM = $product->getName();

                $cart = Mage::helper('freegift')->renderMiniCart();
                $text = Mage::helper('freegift')->renderCartTitle();
                $freegiftbox = Mage::helper('freegift')->renderFreeGiftBox();

                Mage::helper('freegift')->sendResponse($cart, $text, Mage::getUrl('checkout/cart/'), $freegiftbox);
            } else {
                Mage::helper('freegift')->_NAMEITEM = $product->getName();

                $cart = Mage::helper('freegift')->renderBigCart();
                $text = Mage::helper('freegift')->renderCartTitle();
                $freegiftbox = Mage::helper('freegift')->renderFreeGiftBox();
                Mage::helper('freegift')->sendResponse($cart, $text, Mage::getUrl('checkout/cart/'), $freegiftbox);
            }
        }
    }

    public function getChildrenGiftProducts($key)
    {
        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
        $child = array();
        foreach ($items as $item) {
            $params = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
            if ($params['freegift_parent_key'] == $key) {
                $child[] = $item->getProductId();
            }
        }
        return $child;
    }

    public function checkQtyProduct($productId, $qty)
    {
        $cartHelper = Mage::helper('checkout/cart');
        $items = $cartHelper->getCart()->getItems();
        foreach ($items as $item) {
            $prId = $item->getProductId();
            $prQty = $item->getQty();
            if ($productId == $prId) {
                return $qty - $prQty;
            }
        }
        return $qty;
    }

    public function displayFreeGift($productId)
    {
        $freegift = Mage::getModel('catalog/product')->load($productId);
        $productType = $freegift->getTypeID();
        $visibility = $freegift->getVisibility();
        /*if($visibility == MW_FreeGift_Model_Visibility::NOT_VISIBLE_INDIVIDUALLY) return false;*/
        $product_qty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($freegift)->getQty();
        $productIsInStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($freegift)->getIsInStock();
        $qtyNow = $this->checkQtyProduct($freegift->getId(), $product_qty);
        /* no stock */
        $manageStock = $freegift->getStockItem()->getManageStock();
        if ($manageStock) {
            if ($productType == "configurable") {
                if ($productIsInStock == 1 && $freegift->getStatus() == 1) {
                    return true;
                }
            } else {
                if ($qtyNow > 0 && $productIsInStock == 1 && $freegift->getStatus() == 1) {
                    return true;
                }
            }
        } else {
            return true;
        }
        return false;
    }

    public function basDisplayFreegift($productId)
    {
        $freegift_rule = Mage::getModel('freegift/rule')->load($productId);
        $freegift = Mage::getModel('catalog/product')->load($productId);
        $productType = $freegift->getTypeID();
        $visibility = $freegift->getVisibility();
        /*if($visibility == MW_FreeGift_Model_Visibility::NOT_VISIBLE_INDIVIDUALLY) return false;*/
        $product_qty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($freegift)->getQty();
        $productIsInStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($freegift)->getIsInStock();
        if ($productType == "configurable") {
            /**/
        }

        return $freegift->getStatus();
    }

    public function _flagRule()
    {
        return Mage::getSingleton('core/session')->setFlagRule("");
    }

    public function _flagCoupon()
    {
        return Mage::getSingleton('core/session')->setFlagCoupon("");
    }

    public function checkout_submit_all_after($argv)
    {
        $this->_flagCoupon();
        $this->_flagRule();
        return $this;
    }

    protected function addProductWithRule(&$infoRequest, $_product, $quote_item)
    {
        $rule = $this->getAplliedRule($infoRequest);
        if ($rule && in_array($_product->getId(), explode(',', $rule->getData('gift_product_ids')))) {
            if ($rule->getData('discount_qty') && ($rule->getData('discount_qty') > $rule->getData('times_used'))) {

                $this->setDefaultQuoteItem($quote_item);
                $infoRequest['text_gift'] = array(
                    'label' => Mage::helper('freegift')->__((isset($infoRequest['freegift_coupon_code']) || isset($infoRequest['freegift_with_code']) ? 'Free Gift with coupon' : 'Free Gift')),
                    'value' => $rule->getName()
                );

                $quote_item->getOptionByCode('info_buyRequest')->setValue(serialize($infoRequest));

                $quote_item->getQuote()->save();
                $this->_getSession()->getQuote()->setTotalsCollectedFlag(false);
                $this->_getSession()->getQuote()->collectTotals();
                $this->_getSession()->getQuote()->setTotalsCollectedFlag(false);
            }
        } else {
            unset($infoRequest['freegift']);
            unset($infoRequest['freegift_with_code']);
            $quote_item->getOptionByCode('info_buyRequest')->setValue(serialize($infoRequest));
        }
    }

    protected function getQtyToAdded($product, $params, $quote_parent_item = null, $stock_qty)
    {
        $qty_4gift = 0;
        if ($product->getTypeId() == 'configurable') {
            /** Attributes on view product (frontend) */
            $childProduct = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($params['super_attribute'], $product);
            $managechildStock = $childProduct->getStockItem()->getManageStock();
            $childStockQty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct)->getQty();
        } else {
            $manageStock = $product->getStockItem()->getManageStock();
        }
        $qty_4gift = 0;
        if ($product->getTypeId() == 'configurable') {
            if ($managechildStock) {
                if ($childStockQty > 0) {
                    if (isset($params['qty']) && (int)$params['qty'] > $childStockQty) {
                        $qty_4gift = $childStockQty;
                    } else {
                        $qty_4gift = (int)$params['qty'];
                    }
                }
            } else {
                $qty_4gift = (int)$params['qty'];
            }
        } else {
            if ($quote_parent_item && $quote_parent_item->getProductId() == $params['product']) {
                /** If Parent quote product is the same children quote product */
                /* if new quantity greater than stock qty */
                if ($manageStock) {
                    if ((int)$params['qty'] >= $stock_qty) {
                        $qty_4gift = $stock_qty - $params['qty'];
                    } else if (2 * $params['qty'] > $stock_qty) {
                        $qty_4gift = $stock_qty - $params['qty'];
                    } else {
                        $qty_4gift = (int)$params['qty'];
                    }
                } else {
                    $qty_4gift = (int)$params['qty'];
                }
            } else {
                /* if new quantity greater than stock qty */
                $current_stock_qty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
                if ($manageStock) {
                    if ((int)$params['qty'] > $current_stock_qty) {
                        $qty_4gift = $current_stock_qty;
                    } else {
                        $qty_4gift = (int)$params['qty'];
                    }
                } else {
                    $qty_4gift = (int)$params['qty'];
                }
            }
        }
        return $qty_4gift;
    }

    public function checkLicense($o)
    {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array)$modules;
        $modules2 = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        if (!in_array('MW_Mcore', $modules2) || !$modulesArray['MW_Mcore']->is('active') || Mage::getStoreConfig('mcore/config/enabled') != 1) {
            Mage::helper('freegift')->disableConfig();
        }

    }

    public function setDefaultQuoteItem(&$quote_item, $qty = 0)
    {
        $quote_item->setQty($qty);
        $quote_item->setPrice(0);
        $quote_item->setOriginalPrice(0);
        $quote_item->setBaseOriginalPrice(0);
        $quote_item->setCalculationPrice(0);
        $quote_item->setBaseCalculationPrice(0);
        $quote_item->setOriginalCustomPrice(0);
        $quote_item->setCustomPrice(0);
        $quote_item->setRowTotal(0);
        $quote_item->setBasePrice(0);
        $quote_item->setPriceInclTax(0);
        $quote_item->setBasePriceInclTax(0);
        $quote_item->setRowTotalInclTax(0);
        $quote_item->setBaseRowTotalInclTax(0);
        $quote_item->setBaseRowTotal(0);
        $quote_item->setRowTotalWithDiscount(0);
        $quote_item->setBaseRowTotalWithDiscount(0);
        $quote_item->setTaxAmount(0);
        $quote_item->setBaseTaxAmount(0);
        $quote_item->setConvertedPrice(0);
    }
}