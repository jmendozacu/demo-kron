<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Model_Entity_Attribute_Source_DiscountType
    extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const AMOUNT = 0;
    const PERCENTAGE = 1;

    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('tradein')->__('Amount'),
                    'value' =>  self::AMOUNT
                ),
                array(
                    'label' => Mage::helper('tradein')->__('Percentage'),
                    'value' =>  self::PERCENTAGE
                ),
            );
        }
        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * @return array
     */
    public function getSimpleActions()
    {

        /** @var Mage_SalesRule_Model_Rule $salesRuleModel */
        $salesRuleModel = Mage::getModel('salesrule/rule');

        $simpleActions = array(
            self::AMOUNT => $salesRuleModel::BY_FIXED_ACTION,
            self::PERCENTAGE =>$salesRuleModel::BY_PERCENT_ACTION

        );

        return $simpleActions;
    }
}
