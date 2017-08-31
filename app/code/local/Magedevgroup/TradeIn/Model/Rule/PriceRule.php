<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Model_Rule_PriceRule
{
    /**
     * @param $productId int
     * @param $discountType int
     * @param $discountAmount
     * @return mixed
     */
    public function createRule($productId, $discountType, $discountAmount)
    {
        $defaultAction = 'by_fixed';

        $simpleActions = Mage::getModel('magedevgroup_tradein/entity_attribute_source_discountType')
            ->getSimpleActions();

        $simpleAction = isset($simpleActions[$discountType]) ? $simpleActions[$discountType] : $defaultAction;

        $coupon = Mage::getModel('salesrule/rule');

        $coupon->setName('TradeIn Proposal')
            ->setIsActive(1)
            ->setWebsiteIds(array(1))
            ->setCustomerGroupIds(array(1))
            ->setCouponType(2)
            ->setCouponCode(Mage::getModel('magedevgroup_tradein/coupon_codegenerator')
                ->getCouponCode($productId, $discountAmount))
            ->setUsesPerCoupon(1)
            ->setUsesPerCustomer(1)
            ->setConditionsSerialized(
                serialize(
                    array(
                        'type' => 'salesrule/rule_condition_combine',
                        'aggregator' => 'all',
                        'value' => 1,
                    )
                )
            )
            ->setActionsSerialized(
                serialize(
                    array(
                        'type' => 'salesrule/rule_condition_combine',
                        'aggregator' => 'all',
                        'value' => 1,
                        'conditions' => array(
                            array(
                                'type' => 'salesrule/rule_condition_product',
                                'attribute' => 'sku',
                                'operator' => '==',
                                'value' => Mage::getModel('catalog/product')->load($productId)->getData('sku'),
                                'is_value_processed' => false,
                            )
                        )
                    )
                )
            )
            ->setSimpleAction($simpleAction)
            ->setDiscountAmount($discountAmount);

        $coupon->save();

        return $coupon->getCouponCode();
    }
}
