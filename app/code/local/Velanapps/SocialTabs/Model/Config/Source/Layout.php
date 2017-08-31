<?php

class Velanapps_SocialTabs_Model_Config_Source_Layout
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'portrait', 'label' => Mage::helper('adminhtml')->__('Portrait')),
            array('value' => 'landscape', 'label' => Mage::helper('adminhtml')->__('Landscape')),
        );
    }
}
