<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Model_Coupon_Codegenerator
{
    /**
     * Generate coupon code for TradeIn Proposal
     *
     * @param $current_product int  ID of desired product
     * @param $discount_amount int Discount amount for customer
     * @return resource|string TradeIn Proposal Coupon code
     */
    public function getCouponCode($current_product,$discount_amount){
        return 'TRADEIN-'.($current_product+rand(2,9)).($discount_amount+rand(1,7));
    }
}
