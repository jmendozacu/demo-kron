<?php
//class MW_FreeGift_Model_Resource_Coupon extends Mage_Core_Model_Resource_Db_Abstract
class MW_FreeGift_Model_Resource_Coupon extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Constructor adds unique fields
     */
    protected function _construct()
    {
        $this->_init('freegift/coupon', 'coupon_id');
        $this->addUniqueField(array(
            'field' => 'code',
            'title' => Mage::helper('freegift')->__('Coupon with the same code')
        ));
    }

    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getExpirationDate()) {
            $object->setExpirationDate(null);
        } else if ($object->getExpirationDate() instanceof Zend_Date) {
            $object->setExpirationDate($object->getExpirationDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }

        // maintain single primary coupon per rule
        $object->setIsPrimary($object->getIsPrimary() ? 1 : null);

        return parent::_beforeSave($object);
    }

    public function loadPrimaryByRule(MW_FreeGift_Model_Coupon $object, $rule)
    {
        $read = $this->_getReadAdapter();

        if ($rule instanceof MW_FreeGift_Model_Coupon) {
            $ruleId = $rule->getId();
        } else {
            $ruleId = (int)$rule;
        }

        $select = $read->select()->from($this->getMainTable())
            ->where('rule_id = :rule_id');            

        $data = $read->fetchRow($select, array(':rule_id' => $ruleId));

        if (!$data) {
            return false;
        }

        $object->setData($data);

        $this->_afterLoad($object);
        return true;
    }

    public function exists($code)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select();
        $select->from($this->getMainTable(), 'code');
        $select->where('code = :code');

        if ($read->fetchOne($select, array('code' => $code)) === false) {
            return false;
        }
        return true;
    }

    public function updateSpecificCoupons(MW_FreeGift_Model_Coupon $rule)
    {
        if (!$rule || !$rule->getId() || !$rule->hasDataChanges()) {
            return $this;
        }

        $updateArray = array();
        if ($rule->dataHasChangedFor('uses_per_coupon')) {
            $updateArray['usage_limit'] = $rule->getUsesPerCoupon();
        }

        if ($rule->dataHasChangedFor('uses_per_customer')) {
            $updateArray['usage_per_customer'] = $rule->getUsesPerCustomer();
        }

        $ruleNewDate = new Zend_Date($rule->getToDate());
        $ruleOldDate = new Zend_Date($rule->getOrigData('to_date'));

        if ($ruleNewDate->compare($ruleOldDate)) {
            $updateArray['expiration_date'] = $rule->getToDate();
        }

        if (!empty($updateArray)) {
            $this->_getWriteAdapter()->update(
                $this->getTable('freegift/salesrule_coupon'),
                $updateArray,
                array('rule_id = ?' => $rule->getId())
            );
        }

        return $this;
    }
}
