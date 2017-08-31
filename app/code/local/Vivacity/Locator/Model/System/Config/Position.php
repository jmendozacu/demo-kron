<?php
class Vivacity_Locator_Model_System_Config_Position extends Varien_Object
{
    const COL_LEFT	        = 'left';
    const COL_RIGHT	        = 'right';

    public function toOptionArray()
    {
        return array(
            self::COL_LEFT    => Mage::helper('locator')->__('Column Left'),
            self::COL_RIGHT   => Mage::helper('locator')->__('Column Right')
        );
    }
}
