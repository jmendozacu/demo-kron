<?php

class Velanapps_SocialTabs_Model_Config_Source_Pagetype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'profile', 'label' => Mage::helper('adminhtml')->__('Profile')),
            array('value' => 'page', 'label' => Mage::helper('adminhtml')->__('Page')),
            array('value' => 'community', 'label' => Mage::helper('adminhtml')->__('Community')),
        );
    }
}
