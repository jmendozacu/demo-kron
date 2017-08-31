<?php
class MW_FreeGift_Model_Coupon extends Mage_Core_Model_Abstract
{
    /**
     * Coupon's owner rule instance
     *
     * @var Mage_SalesRule_Model_Rule
     */
    protected $_rule;

    protected function _construct()
    {
        parent::_construct();
        $this->_init('freegift/salesrule_coupon');
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if (!$this->getRuleId() && $this->_rule instanceof MW_FreeGift_Model_Rule) {
            $this->setRuleId($this->_rule->getId());
        }
        return parent::_beforeSave();
    }

    /**
     * Set rule instance
     *
     * @param  Mage_SalesRule_Model_Rule
     * @return Mage_SalesRule_Model_Coupon
     */
    public function setRule(MW_FreeGift_Model_Rule $rule)
    {
        $this->_rule = $rule;
        return $this;
    }

    /**
     * Load primary coupon for specified rule
     *
     * @param Mage_SalesRule_Model_Rule|int $rule
     * @return Mage_SalesRule_Model_Coupon
     */
    public function loadPrimaryByRule($rule)
    {
        $this->getResource()->loadPrimaryByRule($this, $rule);
        return $this;
    }

    /**
     * Load Shopping Cart Price Rule by coupon code
     *
     * @param string $couponCode
     * @return Mage_SalesRule_Model_Coupon
     */
    public function loadByCode($couponCode)
    {
        $this->load($couponCode, 'code');
        return $this;
    }
}
