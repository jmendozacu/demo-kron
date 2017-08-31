<?php

class MW_FreeGift_Model_Statusreport extends Varien_Object
{
    const PENDING = 'PENDING'; //haven't change points yet
    const COMPLETE   = 'COMPLETE';
    const UNCOMPLETE = 'UNCOMPLETE';
    const REFUNDED = 'REFUNDED'; // refunded


    static public function getOptionArray()
    {
        return array(
            self::PENDING    => Mage::helper('freegift')->__('Pending'),
            self::COMPLETE   => Mage::helper('freegift')->__('Complete'),
            self::UNCOMPLETE => Mage::helper('freegift')->__('Cancelled'),
        );
    }

    static public function getLabel($type)
    {
        $options = self::getOptionArray();

        return $options[$type];
    }
}