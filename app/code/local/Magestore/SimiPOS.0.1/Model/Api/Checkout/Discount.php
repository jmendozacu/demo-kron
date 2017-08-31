<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * SimiPOS Checkout Cart Coupon / Discount API Model
 * Use to call api with prefix: checkout_discount
 * Methods:
 *  coupon
 *  discount
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Checkout_Discount
    extends Magestore_SimiPOS_Model_Api_Checkout_Abstract
{
    public function apiCoupon($couponCode)
    {
        $quote = $this->_getQuote();
        if (!$quote->getItemsCount()) {
            throw new Exception($this->_helper->__('Quote is empty'), 81);
        }
        if (!strlen($couponCode) && !strlen($quote->getCouponCode())) {
            return false;
        }
        try {
            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 82);
        }
        if ($couponCode) {
            if ($couponCode != $quote->getCouponCode()) {
                throw new Exception($this->_helper->__('Coupon is not valid'), 83);
            }
        }
        return true;
    }
    
    public function apiDiscount($amount, $type = null, $description = null)
    {
        $quote = $this->_getQuote();
        if ($type) { // Percentage
            $quote->setSimiDiscountAmount(0)
                ->setSimiDiscountPercent($amount)
                ->setSimiDiscountDesc($description);
        } else { // Fix amount for hold cart - Default
            $quote->setSimiDiscountAmount($amount)
                ->setSimiDiscountPercent(0)
                ->setSimiDiscountDesc($description);
        }
        try {
            $quote->collectTotals()->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 84); // Cannot setup custom discount
        }
        return true;
    }
}
