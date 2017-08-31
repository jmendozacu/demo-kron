<?php

class Ebizmarts_BakerlooRestful_Model_V1_Coupons extends Ebizmarts_BakerlooRestful_Model_V1_Api {

    protected $_model = "salesrule/coupon";

    /**
     * Validate provided coupon code.
     * Receives an order and validates coupon code.
     *
     * PUT
     */
    public function put() {

        $data = $this->getJsonPayload();

        //Apply coupon and validate
        $couponCode = $data->coupon_code;

        if(empty($couponCode)) {
            Mage::throwException('Invalid coupon code.');
        }

        $quote = Mage::helper('bakerloo_restful/sales')->buildQuote($this->getStoreId(), $data, true);

        $quote->setCouponCode(strlen($couponCode) ? $couponCode : '')
            ->collectTotals()
            ->save();

        if ($couponCode != $quote->getCouponCode()) {
            //DELETE quote so we don't leave garbage in db
            $quote->delete();

            $errorMessage = Mage::helper('bakerloo_restful/sales')->__('Coupon code `%s` is not valid.', Mage::helper('core')->escapeHtml($couponCode));
            Mage::throwException($errorMessage);
        }

        $coupon = Mage::getModel('salesrule/coupon');
        /** @var Mage_SalesRule_Model_Coupon */
        $coupon->load($couponCode, 'code');
        if ($coupon->getId()) {
            $ruleId = $coupon->getRuleId();
            $rule = Mage::getModel('salesrule/rule')->load($ruleId);

            $cartData = array(
                'quote_currency_code'         => $quote->getQuoteCurrencyCode(),
                'grand_total'                 => $quote->getGrandTotal(),
                'base_grand_total'            => $quote->getBaseGrandTotal(),
                'sub_total'                   => $quote->getSubtotal(),
                'base_subtotal'               => $quote->getBaseSubtotal(),
                'subtotal_with_discount'      => $quote->getSubtotalWithDiscount(),
                'base_subtotal_with_discount' => $quote->getBaseSubtotalWithDiscount(),
                'items'                       => array(),
            );

            foreach($quote->getItemsCollection(false) as $quoteItem) {

                if ($quoteItem->getParentItem()) {
                    continue;
                }

                $item = array(
                    'sku'                     => $quoteItem->getSku(),
                    'product_id'              => (int)$quoteItem->getProductId(),
                    'qty'                     => $quoteItem->getQty(),
                    'price'                   => $quoteItem->getPrice(),
                    'price_incl_tax'          => $quoteItem->getPriceInclTax(),
                    'base_price_incl_tax'     => (float)$quoteItem->getBasePriceInclTax(),
                    'row_total'               => $quoteItem->getRowTotal(),
                    'row_total_with_discount' => (float)$quoteItem->getRowTotalWithDiscount(),
                    'row_total_incl_tax'      => $quoteItem->getRowTotalInclTax(),
                    'base_row_total'          => $quoteItem->getBaseRowTotal(),
                    'custom_price'            => (float)$quoteItem->getCustomPrice(),
                    'discount_amount'         => $quoteItem->getDiscountAmount(),
                    'tax_amount'              => (float)$quoteItem->getTaxAmount(),
                );

                $cartData['items'][] = $item;
            }

            $returnData = array(
                'valid'             => true,
                'coupon_code'       => $rule->getCouponCode(),
                'uses_per_coupon'   => (int)$rule->getUsesPerCoupon(),
                'uses_per_customer' => (int)$rule->getUsesPerCustomer(),
                'times_used'        => (int)$rule->getTimesUsed(),
                'discount_amount'   => (float)$rule->getDiscountAmount(),
                'discount_type'     => $rule->getSimpleAction(),
                'name'              => $rule->getName(),
                'description'       => $rule->getDescription(),
                'order'             => $cartData,
            );

            //DELETE quote so we don't leave garbage in db
            $quote->delete();

        }
        else {
            //DELETE quote so we don't leave garbage in db
            $quote->delete();

            Mage::throwException('Coupon does not exist.');
        }



        return $returnData;

    }

}