<?php
class MW_FreeGift_Model_Resource_Coupon_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('freegift/coupon');
    }

    public function addRuleToFilter($rule)
    {	
        if ($rule instanceof MW_FreeGift_Model_Salesrule) {
            $ruleId = $rule->getId();
        } else {
            $ruleId = (int)$rule;
        }

        $this->addFieldToFilter('rule_id', $ruleId);
		
        return $this;
    }

    public function addRuleIdsToFilter(array $ruleIds)
    {
        $this->addFieldToFilter('rule_id', array('in' => $ruleIds));
        return $this;
    }

    public function addGeneratedCouponsFilter()
    {        
        return $this;
    }

    public function addIsUsedFilterCallback($collection, $column)
    {	
        $filterValue = $column->getFilter()->getCondition();
        $collection->addFieldToFilter(
            $this->getConnection()->getCheckSql('main_table.times_used > 0', 1, 0),
            array('eq' => $filterValue)
        );
    }
}
