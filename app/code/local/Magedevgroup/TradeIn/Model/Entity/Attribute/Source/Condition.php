<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Model_Entity_Attribute_Source_Condition extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const PERFECT = 0;
    const SCRATCH = 1;
    const DAMAGED = 2;
    const NOTWORK = 3;

    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('tradein')->__('Perfect'),
                    'value' =>  self::PERFECT
                ),
                array(
                    'label' => Mage::helper('tradein')->__('Few marks/scratches'),
                    'value' =>  self::SCRATCH
                ),
                array(
                    'label' => Mage::helper('tradein')->__('Major marks/damage'),
                    'value' =>  self::DAMAGED
                ),
                array(
                    'label' => Mage::helper('tradein')->__('Not Working'),
                    'value' =>  self::NOTWORK
                ),
            );
        }
        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    static public function getOptionArray()
    {
        return [
            self::PERFECT => Mage::helper('tradein')->__('Perfect'),
            self::SCRATCH => Mage::helper('tradein')->__('Few marks/scratches'),
            self::DAMAGED => Mage::helper('tradein')->__('Major marks/damage'),
            self::NOTWORK => Mage::helper('tradein')->__('Not Working'),
        ];
    }
}
