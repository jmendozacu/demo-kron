<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Model_Entity_Attribute_Source_Status extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    const WAIT = 0;
    const DECLINE = 1;
    const ACCEPT = 2;
    const COUPSEND = 3;
    const SHIPSEND = 4;

    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => Mage::helper('tradein')->__('Wait to approval'),
                    'value' =>  self::WAIT
                ),
                array(
                    'label' => Mage::helper('tradein')->__('Proposal Decline'),
                    'value' =>  self::DECLINE
                ),
                array(
                    'label' => Mage::helper('tradein')->__('Proposal Accept'),
                    'value' =>  self::ACCEPT
                ),
                array(
                    'label' => Mage::helper('tradein')->__('Email with coupon was send'),
                    'value' =>  self::COUPSEND
                ),
                array(
                    'label' => Mage::helper('tradein')->__('Shipping Email was send'),
                    'value' =>  self::SHIPSEND
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
            self::WAIT    => Mage::helper('tradein')->__('Wait to approval'),
            self::DECLINE => Mage::helper('tradein')->__('Proposal Decline'),
            self::ACCEPT  => Mage::helper('tradein')->__('Proposal Accept'),
            self::COUPSEND   => Mage::helper('tradein')->__('Email with coupon was send'),
            self::SHIPSEND   => Mage::helper('tradein')->__('Shipping Email was send'),
        ];
    }
}
