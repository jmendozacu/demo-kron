<?php
class MW_FreeGift_Model_Resource_Salesrule_Coupon_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('freegift/salesrule_coupon');
    }

    /**
     * Add rule to filter
     *
     * @param Mage_SalesRule_Model_Rule|int $rule
     *
     * @return Mage_SalesRule_Model_Resource_Coupon_Collection
     */
    public function addRuleToFilter($rule)
    {
        if ($rule instanceof MW_FreeGift_Model_Rule) {
            $ruleId = $rule->getId();
        } else {
            $ruleId = (int)$rule;
        }

        $this->addFieldToFilter('rule_id', $ruleId);

        return $this;
    }

    /**
     * Add rule IDs to filter
     *
     * @param array $ruleIds
     *
     * @return Mage_SalesRule_Model_Resource_Coupon_Collection
     */
    public function addRuleIdsToFilter(array $ruleIds)
    {
        $this->addFieldToFilter('rule_id', array('in' => $ruleIds));
        return $this;
    }

    /**
     * Filter collection to be filled with auto-generated coupons only
     *
     * @return Mage_SalesRule_Model_Resource_Coupon_Collection
     */
    public function addGeneratedCouponsFilter()
    {        
        return $this;
    }

    /**
     * Callback function that filters collection by field "Used" from grid
     *
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     */
    public function addIsUsedFilterCallback($collection, $column)
    {
        $filterValue = $column->getFilter()->getCondition();
        $collection->addFieldToFilter(
            $this->getConnection()->getCheckSql('main_table.times_used > 0', 1, 0),
            array('eq' => $filterValue)
        );
    }
}
