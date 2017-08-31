<?php

/**
 * Created by PhpStorm.
 * User: Quoc Viet
 * Date: 07/07/2015
 * Time: 9:53 SA
 *
 * Edited by NetBeans.
 * User: Daniel
 * Date: 21/01/2016
 * Time: 05:49 PM
 */
class Magestore_Webpos_Model_Role extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('webpos/role');
    }

    public function toOptionArray() {
        $options = array();
        $roleCollection = $this->getCollection()->addFieldToFilter('active', 1);
        if ($roleCollection->getSize() > 1) {
            $options = array('' => '---Select Role---');
        }
        foreach ($roleCollection as $role) {
            $key = $role->getId();
            $value = $role->getDisplayName();
            $options [$key] = $value;
        }
        return $options;
    }

    public function getMaximumDiscountPercent() {
        return $this->getData('maximum_discount_percent');
    }

}
