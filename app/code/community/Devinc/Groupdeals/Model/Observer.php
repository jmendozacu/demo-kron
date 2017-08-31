<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Checkout observer model
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Devinc_Groupdeals_Model_Observer
{
    /**
     * Product qty's checked
     * data is valid if you check quote item qty and use singleton instance
     *
     * @var array
     */
    protected $_checkedQuoteItems = array();

    //ADMIN Events
    //launch groupdeal index refresh after reindexing or saving product or category in admin panel
    public function hookToControllerActionPostDispatch($observer)
    {
        $actionName = $observer->getEvent()->getControllerAction()->getFullActionName();

        if ($actionName == 'adminhtml_process_massReindex' || $actionName == 'adminhtml_process_reindexProcess'
            || $actionName == 'adminhtml_catalog_category_save' || $actionName == 'adminhtml_catalog_product_save'
        ) {
            Mage::dispatchEvent("refresh_indexes", array('request' => $observer->getControllerAction()->getRequest()));
        }
    }

    //verify on login if merchant, in order to display the merchant account menu item
    public function merchantVerification($observer)
    {
        if (Mage::getModel('groupdeals/merchants')->isMerchant()) {
            Mage::getModel('core/config')->saveConfig('groupdeals/is_merchant', 1, 'default', 0);
        } else {
            Mage::getModel('core/config')->saveConfig('groupdeals/is_merchant', 0, 'default', 0);
        }
    }

    //refresh groupdeal indexes
    public function refreshIndexes()
    {
        $stores = Mage::app()->getStores();
        foreach ($stores as $_eachStoreId => $val) {
            $store = Mage::app()->getStore($_eachStoreId);
            if ($store->getRootCategoryId()) {
                $groupdealsCollection = Mage::getModel('groupdeals/groupdeals')->getCollection();

                if (count($groupdealsCollection) > 0) {
                    foreach ($groupdealsCollection as $groupdeal) {
                        $_storeId = $store->getId();
                        $groupdealId = $groupdeal->getId();
                        $productId = $groupdeal->getProductId();
                        $product = Mage::getModel('catalog/product')->setStoreId($_storeId)->load($productId);

                        Mage::getSingleton('catalog/url')->refreshProductRewrite($productId, $_storeId);
                        $productUrlRewrite = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('store_id', $_storeId)->addFieldToFilter('target_path', 'catalog/product/view/id/' . $productId)->getFirstItem();
                        if ($productUrlRewrite->getId()) {
                            $productUrlRewrite->setTargetPath('groupdeals/product/view/id/' . $productId . '/groupdeals_id/' . $groupdealId)->save();
                        }

                        if (count($product->getCategoryIds()) > 0) {
                            foreach ($product->getCategoryIds() as $categoryId) {
                                $categoryUrlRewrite = Mage::getModel('core/url_rewrite')->getCollection()->addFieldToFilter('store_id', $_storeId)->addFieldToFilter('target_path', 'catalog/product/view/id/' . $productId . '/category/' . $categoryId)->getFirstItem();
                                if ($categoryUrlRewrite->getId()) {
                                    $categoryUrlRewrite->setTargetPath('groupdeals/product/view/id/' . $productId . '/groupdeals_id/' . $groupdealId . '/category/' . $categoryId)->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    //delete deal if product is deleted from catalog->manage deals
    public function deleteDeal($observer)
    {
        $productId = $observer->getEvent()->getProduct()->getId();
        $groupdeals = Mage::getModel('groupdeals/groupdeals')->load($productId, 'product_id');
        $groupdeals->delete();
    }

    //delete coupon if order gets canceled
    public function deleteCoupon($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $items = $order->getItemsCollection();

        if (count($items) > 0) {
            foreach ($items as $item) {
                $couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('order_item_id', $item->getId());
                if (count($couponsCollection) > 0) {
                    foreach ($couponsCollection as $coupon) {
                        $coupon->delete();
                    }
                }
            }
        }
    }

    //function DEPRECATED
    //creates redirect to the deal/city page if a city is selected under Group Deals->Settings->Configuration->Homepage Deals
    public function setGroupdealsRedirect($observer)
    {
        $store = $observer->getEvent()->getStore();
        $website = $observer->getEvent()->getWebsite();
        $scope = 'default';
        $scopeId = 0;
        $storeId = 0;
        if ($store && $store != '') {
            $scope = 'stores';
            $scopeId = Mage::getModel('core/store')->load($store, 'code')->getId();
            $storeId = $scopeId;
        } elseif ($website && $website != '') {
            $scope = 'websites';
            $scopeId = Mage::getModel('core/website')->load($website, 'code')->getId();
            $storeId = Mage::getModel('core/store')->load($scopeId, 'website_id')->getId();
        }
        $previousPath = Mage::getStoreConfig('web/default/front', $storeId);
        $redirectPath = 'groupdeals/product/redirect';
        if (Mage::getStoreConfig('groupdeals/configuration/homepage_deals', $storeId) != 'default') {
            $path = $redirectPath;
            if ($previousPath != $redirectPath) {
                Mage::getModel('core/config')->saveConfig('groupdeals/previous_homepage_path', $previousPath, $scope, $scopeId);
            }
        } else {
            if ($previousPath != $redirectPath) {
                $path = $previousPath;
            } else {
                $path = Mage::getStoreConfig('groupdeals/previous_homepage_path', $storeId);
            }
        }

        Mage::getModel('core/config')->saveConfig('web/default/front', $path, $scope, $scopeId);
    }

    //redirect the homepage to the deal page
    public function homepageRedirect($observer)
    {
        if (Mage::app()->getStore()->getCode() != 'admin') {
            $url = Mage::helper('core/url')->getCurrentUrl();

            if (substr($url, -1) != '/') {
                $url .= '/';
            }
            if (Mage::getStoreConfig('web/url/use_store') && strpos($url, Mage::app()->getStore()->getCode()) === false) {
                $url .= Mage::app()->getStore()->getCode() . '/';
            }

            $baseUrl = Mage::getBaseUrl();
            $baseUrlNoIndex = str_replace('index.php/', '', $baseUrl);
            $path = str_replace($baseUrlNoIndex, '', str_replace($baseUrl, '', $url));
            if ($path == '' || $path == '/' || $path == '/index.php' || $path == '/index.php/' || $path == '/home' || $path == '/home/') {
                $helper = Mage::helper('groupdeals');
                $storeId = Mage::app()->getStore()->getId();
                $city = Mage::getStoreConfig('groupdeals/configuration/homepage_deals', $storeId);

                if ($helper->isEnabled() && $city != 'default') {
                    $redirectUrl = Mage::helper('groupdeals')->getCityUrl($city);
                    if ($redirectUrl == '') {
                        $redirectUrl = Mage::getUrl(Mage::getStoreConfig('web/default/cms_home_page'));
                    }

                    $response = $observer->getEvent()->getResponse();
                    $response->setRedirect($redirectUrl);
                }
            }
        }
    }

    //generate coupons on order invoice
    public function createCouponsAfterInvoice($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $items = $order->getItemsCollection();

        foreach ($items as $item) {
            $groupdeal = Mage::getModel('groupdeals/groupdeals')->load($item->getProductId(), 'product_id');
            if ($groupdeal->getId()) {
                //create coupons if virtual deal
                if ($item->getIsVirtual()) {
                    $createdCoupons = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('order_item_id', $item->getId())->count();
                    $count = $item->getQtyInvoiced() - $createdCoupons;
                    for ($i = 0; $i < $count; $i++) {
                        $random = rand(10e16, 10e20);
                        $couponCode = strtoupper(base_convert($random, 10, 36));
                        $coupon = Mage::getModel('groupdeals/coupons')
                            ->setGroupdealsId($groupdeal->getId())
                            ->setOrderItemId($item->getId())
                            ->setCouponCode($couponCode)
                            ->setRedeem('not_used')
                            ->setStatus('pending')
                            ->save();
                    }
                }
            }
        }
    }

    //void coupons on refund
    public function voidCouponsAfterRefund($observer)
    {
        $order = $observer->getEvent()->getPayment()->getOrder();
        $items = $order->getItemsCollection();

        foreach ($items as $item) {
            $groupdeal = Mage::getModel('groupdeals/groupdeals')->load($item->getProductId(), 'product_id');
            if ($groupdeal->getId()) {
                //void coupons if virtual deal
                if ($item->getIsVirtual()) {
                    $voidedCoupons = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('order_item_id', $item->getId())->addFieldToFilter('status', 'voided')->count();
                    $count = $item->getQtyRefunded() - $voidedCoupons;
                    for ($i = 0; $i < $count; $i++) {
                        $coupon = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('order_item_id', $item->getId())->addFieldToFilter('status', array('neq' => 'voided'))->getFirstItem();
                        $coupon->setStatus('voided')->save();
                    }
                }
            }
        }
    }

    //FRONTEND Events
    //init groupdeal
    //set deal city
    //refresh groupdeal if time is over or is out of stock and it's still running
    //redirect to noRoute page if deal's status is not running or ended
    public function validateGroupdeal($observer)
    {
        $product = $observer->getEvent()->getProduct();
        //$groupdeal = Mage::getModel('groupdeals/groupdeals')->load($product->getId(), 'product_id');
        $groupdeal = Mage::registry('groupdeals');

        if ($groupdeal) {
            //init groupdeal
            //Mage::register('groupdeals', $groupdeal);

            //allowed deal status array
            $statusArray = array();
            $statusArray[] = Devinc_Groupdeals_Model_Source_Status::STATUS_RUNNING;
            $statusArray[] = Devinc_Groupdeals_Model_Source_Status::STATUS_ENDED;

            if (in_array($product->getGroupdealStatus(), $statusArray)) {
                //set region/city
                $crcId = $observer->getEvent()->getControllerAction()->getRequest()->getParam('crc', false);
                if ($crcId) {
                    $crc = Mage::getModel('groupdeals/crc')->load($crcId);
                } else {
                    $crc = Mage::getModel('groupdeals/crc')->getMainCrc($groupdeal->getId());
                }
                $helper = Mage::helper('groupdeals');
                $helper->setCity($crc->getCity());
                $helper->setRegion($crc->getRegion());

                //refresh deal if no longer saleable
                if (!$product->isSaleable()) {
                    Mage::getModel('groupdeals/groupdeals')->refreshGroupdeal($product);
                }
            } else if (!isset($_GET['deal_preview']) || (isset($_GET['deal_preview']) && $_GET['deal_preview'] != 1)) {
                throw new Mage_Core_Exception('Deal is not visible.');
            }
        }
    }

    //verify if groupdeal is expired; if yes, it doesn't allow the user to add to cart
    public function isGroupdealSaleable($observer)
    {
        $object = $observer->getEvent()->getSalable();
        $storeId = Mage::app()->getStore()->getId();
        $productId = $observer->getEvent()->getProduct()->getId();
        $helper = Mage::helper('groupdeals');
        $groupdeal = Mage::getModel('groupdeals/groupdeals')->load($productId, 'product_id');

        if ($groupdeal->getId()) {
            $currentDateTime = $helper->getCurrentDateTime();
            $product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId);
            if ($currentDateTime >= $product->getGroupdealDatetimeTo() || !$helper->isEnabled() || $product->getGroupdealStatus() != Devinc_Groupdeals_Model_Source_Status::STATUS_RUNNING) {
                if (!isset($_GET['deal_preview']) || (isset($_GET['deal_preview']) && $_GET['deal_preview'] != 1) || (isset($_GET['deal_preview']) && $_GET['deal_preview'] == 1 && $product->getGroupdealStatus() != Devinc_Groupdeals_Model_Source_Status::STATUS_QUEUED)) {
                    $object->setIsSalable(false);
                }
            }
        }
    }

    //adds the deals region/city to the quote item; their used in the shopping cart on the product url and when placing an order to subscribe the customers
    public function setRegionCityToQuoteItem($observer)
    {
        $quoteItem = $observer->getEvent()->getQuoteItem();
        $helper = Mage::helper('groupdeals');
        $region = $helper->getRegion();
        $city = $helper->getCity();

        //in case added from list pages, check to see if sessions region/city belong to deal; if not get deal's main country/region/city id
        $crc = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('product_id', $quoteItem->getProductId())->addFieldToFilter('region', $region)->addFieldToFilter('city', $city)->getFirstItem();
        if (!$crc->getId()) {
            $crc = Mage::getModel('groupdeals/crc')->getProductMainCrc($quoteItem->getProductId());
        }

        if ($crc->getId()) {
            $quoteItem->setCrcId($crc->getId());
        }
    }

    //opens gift popup on shopping cart page if product is added via the Give as a Gift button
    public function openGiftPopup($observer)
    {
        Mage::getSingleton('core/session')->setGiftDeal();
        $gift = $observer->getEvent()->getRequest()->getParam('gift', false);

        if ($gift) {
            Mage::getSingleton('core/session')->setGiftDeal(true);
        }
    }

    /**
     * runs at checkout pages. checks to see if the deals have reached their maximum order qty per customer
     *
     * @param $observer
     */
    public function reviewCartItem($observer)
    {
        if (Mage::helper('groupdeals')->isEnabled()) {
            $item = $observer->getEvent()->getItem();
            $quote = $item->getQuote();
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            $groupdeal = Mage::getModel('groupdeals/groupdeals')->load($product->getId(), 'product_id');

            if ($groupdeal->getId()) {
                $totalQty = $this->_getQuoteItemQtyForCheck(
                    $groupdeal, $product->getId(), $item->getId(), $item->getQty()
                );
                $maxQty = $groupdeal->getMaximumQty();

                if ($product->getGroupdealStatus() != Devinc_Groupdeals_Model_Source_Status::STATUS_RUNNING) {
                    $message = Mage::helper('groupdeals')->
                    __('The &#34;%s&#34; DEAL is no longer available for purchase.', $product->getName());
                    $item->setHasError(true);
                    $item->setMessage($message);
                    $quote->setHasError(true);
                    $quote->addMessage($message);
                } else if ($maxQty < $totalQty) {
                    $message = Mage::helper('groupdeals')->__(
                        'The maximum order qty available for the &#34;%s&#34; DEAL is %s. P
                    lease take into account your previous purchases as well.',
                        $product->getName(), $maxQty
                    );

                    $item->setHasError(true);
                    $item->setMessage($message);
                    $quote->setHasError(true);
                    $quote->addMessage($message);
                } else {
                    $message = Mage::helper('groupdeals')->__(
                        'The maximum order qty available for the &#34;%s&#34; DEAL is %s. 
                    Please take into account your previous purchases as well.', $product->getName(), $maxQty
                    );
                    $item->removeMessageByText($message);
                    $quote->removeMessageByText('error', $message);
                    $message = Mage::helper('groupdeals')->
                    __('The &#34;%s&#34; DEAL is no longer available for purchase.', $product->getName());
                    $item->removeMessageByText($message);
                    $quote->removeMessageByText('error', $message);
                    $errors = $quote->getErrors();
                    if (count($errors) == 0) {
                        $quote->setHasError(false);
                    }
                }
            }
        }
    }
    /**
     * Retrieves the total product qty from the cart;
     * taking into account the qty of associated products or custom options from
     * the cart as well as the customers previous orders.
     *
     * @param  $groupdeal
     * @param  $productId
     * @param  $quoteItemId
     * @param  $itemQty
     * @return int
     */
    protected function _getQuoteItemQtyForCheck($groupdeal, $productId, $quoteItemId, $itemQty)
    {
        $qty = $itemQty;
        if (isset($this->_checkedQuoteItems[$productId]['qty']) &&
            !in_array($quoteItemId, $this->_checkedQuoteItems[$productId]['items'])
        ) {
            $qty += $this->_checkedQuoteItems[$productId]['qty'];
        }

        if (!isset($this->_checkedQuoteItems[$productId]['prev_orders_qty'])) {
            $customerSession = Mage::getSingleton('customer/session');
            $prevOrdersQty = 0;
            if ($customerSession->isLoggedIn()) {
                $customer = Mage::getModel('customer/customer')->load($customerSession->getCustomerId());
                $prevOrdersQty = Mage::getModel('groupdeals/groupdeals')->getGroupdealsSoldQty(
                    $groupdeal, $customer->getEmail()
                );
            }
            $this->_checkedQuoteItems[$productId]['prev_orders_qty'] = $prevOrdersQty;
            $qty += $prevOrdersQty;
        }

        $this->_checkedQuoteItems[$productId]['qty'] = $qty;
        $this->_checkedQuoteItems[$productId]['items'][] = $quoteItemId;

        return $qty;
    }

    //add gift values to quote
    public function quoteMergeGift($observer)
    {
        $quote = $observer->getEvent()->getSource();
        $customerQuote = $observer->getEvent()->getQuote();

        $customerQuote->setGroupdealsCouponFrom($quote->getGroupdealsCouponFrom());
        $customerQuote->setGroupdealsCouponTo($quote->getGroupdealsCouponTo());
        $customerQuote->setGroupdealsCouponToEmail($quote->getGroupdealsCouponToEmail());
        $customerQuote->setGroupdealsCouponMessage($quote->getGroupdealsCouponMessage());
    }

    //subscribe customer when order has been placed
    public function subscribeCustomer($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $items = $order->getItemsCollection();

        foreach ($items as $item) {
            $groupdeal = Mage::getModel('groupdeals/groupdeals')->load($item->getProductId(), 'product_id');
            if ($groupdeal->getId()) {
                //verify if subscribed, if not then subscribe customer
                $storeId = Mage::app()->getStore()->getId();
                $customerEmail = $order->getCustomerEmail();
                $crc = Mage::getModel('groupdeals/crc')->load($item->getCrcId());
                $city = $crc->getCity();
                $subscribersCollection = Mage::getModel('groupdeals/subscribers')->getCollection()->addFieldToFilter('store_id', $storeId)->addFieldToFilter('email', $customerEmail)->addFieldToFilter('city', $city);
                if (count($subscribersCollection) == 0 && $customerEmail != '' && $city != '') {
                    $subscriberData['email'] = $customerEmail;
                    $subscriberData['city'] = $city;
                    $subscriberData['store_id'] = $storeId;

                    $model = Mage::getModel('groupdeals/subscribers');
                    $model->setData($subscriberData);
                    $model->save();
                }
            }
        }
    }
}
