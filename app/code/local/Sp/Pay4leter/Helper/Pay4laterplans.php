<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * MinSaleQty value manipulation helper
 */
class Sp_Pay4leter_Helper_Pay4laterplans
{
    /**
     * Retrieve fixed qty value
     *
     * @param mixed $qty
     * @return float|null
     */
    protected function _fixPlan($plan)
    {
        return (!empty($plan) ? (float)$plan : null);
    }

    /**
     * Generate a storable representation of a value
     *
     * @param mixed $value
     * @return string
     */
    protected function _serializeValue($value)
    {

        if (is_numeric($value)) {
            $data = (float)$value;
            return (string)$data;
        } else if (is_array($value)) {
            $data = array();
            foreach ($value as $groupId => $plan) {
                
                if (!array_key_exists($groupId, $data)) {

                    $data[$groupId]=array(
                            'plan_label'=>$plan['plan_label'],
                            'plan_code'=>$plan['plan_code'],
                            'deposite_1'=>$this->_fixPlan($plan['deposite_1']),
                            'deposite_2'=>$this->_fixPlan($plan['deposite_2']),
                            'deposite_3'=>$this->_fixPlan($plan['deposite_3']),
                            'deposite_4'=>$this->_fixPlan($plan['deposite_4']),
                            'deposite_5'=>$this->_fixPlan($plan['deposite_5']),
                        );
                    //$data[$groupId] = $this->_fixPlan($plan);
                }
            }
        
            if (count($data) == 1 && array_key_exists(Mage_Customer_Model_Group::CUST_GROUP_ALL, $data)) {
                return (string)$data[Mage_Customer_Model_Group::CUST_GROUP_ALL];
            }
            return serialize($data);
        } else {
            return '';
        }
    }

    /**
     * Create a value from a storable representation
     *
     * @param mixed $value
     * @return array
     */
    public function _unserializeValue($value)
    {
        if (is_string($value) && !empty($value)) {
            return unserialize($value);
        } else {
            return array();
        }
    }

    /**
     * Check whether value is in form retrieved by _encodeArrayFieldValue()
     *
     * @param mixed
     * @return bool
     */
    protected function _isEncodedArrayFieldValue($value)
    {
        if (!is_array($value)) {
            return false;
        }
        unset($value['__empty']);
        foreach ($value as $_id => $row) {
            if (!is_array($row) || !array_key_exists('plan_label', $row) || !array_key_exists('plan_code', $row)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Encode value to be used in Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
     *
     * @param array
     * @return array
     */
    protected function _encodeArrayFieldValue(array $value)
    {
        $result = array();
        foreach ($value as $groupId => $plan) {
            $_id = Mage::helper('core')->uniqHash('_');
            $result[$_id] = array(
                'plan_label' => $groupId,
                'plan_code' => $plan['plan_code'],
                'deposite_1' => $this->_fixPlan($plan['deposite_1']),
                'deposite_2' => $this->_fixPlan($plan['deposite_2']),
                'deposite_3' => $this->_fixPlan($plan['deposite_3']),
                'deposite_4' => $this->_fixPlan($plan['deposite_4']),
                'deposite_5' => $this->_fixPlan($plan['deposite_5'])
            );
        }
        return $result;
    }

    /**
     * Decode value from used in Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
     *
     * @param array
     * @return array
     */
    protected function _decodeArrayFieldValue(array $value)
    {
        $result = array();
        unset($value['__empty']);
        foreach ($value as $_id => $row) {
            if (!is_array($row) || !array_key_exists('plan_label', $row) || !array_key_exists('deposite_1', $row)) {
                continue;
            }
            
            $groupId = $_id;
            $result[$groupId] = array(
                'plan_label' => $row['plan_label'],
                'plan_code' => $row['plan_code'],
                'deposite_1' => $this->_fixPlan($row['deposite_1']),
                'deposite_2' => $this->_fixPlan($row['deposite_2']),
                'deposite_3' => $this->_fixPlan($row['deposite_3']),
                'deposite_4' => $this->_fixPlan($row['deposite_4']),
                'deposite_5' => $this->_fixPlan($row['deposite_5'])
            );
        }
        return $result;
    }

    /**
     * Retrieve min_sale_qty value from config
     *
     * @param int $customerGroupId
     * @param mixed $store
     * @return float|null
     */
    public function getConfigValue()
    {
        $value = Mage::getStoreConfig('payment/dynamic_plans');
        $value = $this->_unserializeValue($value['dynamic_pay4later_plans']);
        if ($this->_isEncodedArrayFieldValue($value)) {
            $value = $this->_decodeArrayFieldValue($value);
        }
        $result = array();
        foreach($value as $code){
            $result[$code['plan_code']] = $code['plan_label'];
        }
        return $result;
    }

    /**
     * Make value readable by Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
     *
     * @param mixed $value
     * @return array
     */
    public function makeArrayFieldValue($value)
    {
        $value = $this->_unserializeValue($value);
        if (!$this->_isEncodedArrayFieldValue($value)) {
            $value = $this->_encodeArrayFieldValue($value);
        }
        return $value;
    }

    /**
     * Make value ready for store
     *
     * @param mixed $value
     * @return string
     */
    public function makeStorableArrayFieldValue($value)
    {
        if ($this->_isEncodedArrayFieldValue($value)) {
            $value = $this->_decodeArrayFieldValue($value);
        }
        $value = $this->_serializeValue($value);
        return $value;
    }
}
