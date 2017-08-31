<?php
    class MW_FreeGift_Model_System_Config_Showfreegiftpromotion extends Mage_Core_Model_Abstract
    {
        public function toOptionArray()
        {
            return array(
                array('value' => 1, 'label'=>Mage::helper('freegift')->__('Yes, show it on cart')),
                array('value' => 2, 'label'=>Mage::helper('freegift')->__('Yes, show it on checkout')),
                array('value' => 3, 'label'=>Mage::helper('freegift')->__('Yes, show it on cart and checkout')),            
                array('value' => 4, 'label'=>Mage::helper('freegift')->__('No, hide it')),            
            );        
        }
    }
?>