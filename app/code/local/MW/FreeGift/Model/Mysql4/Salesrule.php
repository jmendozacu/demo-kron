<?php

class MW_FreeGift_Model_Mysql4_Salesrule extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the freegift_id refers to the key field in your database table.
        $this->_init('freegift/salesrule', 'rule_id');
    }
    public function getActiveAttributes($websiteId, $customerGroupId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('a' => $this->getTable('freegift/product_attribute')),
                new Zend_Db_Expr('DISTINCT ea.attribute_code'))
            ->joinInner(array('ea' => $this->getTable('eav/attribute')), 'ea.attribute_id = a.attribute_id', '')
            ;
        return $read->fetchAll($select);
    }
    public function setActualProductAttributes($rule, $attributes)
    {
        $write = $this->_getWriteAdapter();
        $write->delete($this->getTable('freegift/product_attribute'),
            $write->quoteInto('rule_id=?', $rule->getId()));

        //Getting attribute IDs for attribute codes
        $attributeIds = array();
        $select = $this->_getReadAdapter()->select()
                ->from(array('a'=>$this->getTable('eav/attribute')), array('a.attribute_id'))
                ->where('a.attribute_code IN (?)', array($attributes));
        $attributesFound = $this->_getReadAdapter()->fetchAll($select);
        if ($attributesFound) {
            foreach ($attributesFound as $attribute) {
                $attributeIds[] = $attribute['attribute_id'];
            }

            $data = array();
            foreach (explode(',', $rule->getCustomerGroupIds()) as $customerGroupId) {
                foreach (explode(',', $rule->getWebsiteIds()) as $websiteId) {
                    foreach ($attributeIds as $attribute) {
                        $data[] = array (
                            'rule_id'           => $rule->getId(),
                            'website_id'        => $websiteId,
                            'customer_group_id' => $customerGroupId,
                            'attribute_id'      => $attribute
                        );
                    }
                }
            }
            $write->insertMultiple($this->getTable('freegift/product_attribute'), $data);
        }
        return $this;
    }
}