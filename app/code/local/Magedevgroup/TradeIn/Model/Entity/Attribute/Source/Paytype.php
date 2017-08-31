<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Model_Entity_Attribute_Source_Paytype extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const PAYPAL = 0;
    const SAGEPAY = 1;
    const PAY4LATER = 2;

    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('tradein')->__('PayPal'),
                    'value' =>  self::PAYPAL
                ),
                array(
                    'label' => Mage::helper('tradein')->__('SagePay'),
                    'value' =>  self::SAGEPAY
                ),
                array(
                    'label' => Mage::helper('tradein')->__('Pay4Later'),
                    'value' =>  self::PAY4LATER
                ),
            );
        }
        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
