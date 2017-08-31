<?php
    class MW_SocialGift_Model_System_Config_Showsocialgiftpromotion extends Mage_Core_Model_Abstract
    {
        public function toOptionArray()
        {
            return array(
                array('value' => 1, 'label'=>Mage::helper('mw_socialgift')->__('Yes, show it on cart')),
                array('value' => 2, 'label'=>Mage::helper('mw_socialgift')->__('Yes, show it on checkout')),
                array('value' => 3, 'label'=>Mage::helper('mw_socialgift')->__('Yes, show it on cart and checkout')),            
                array('value' => 4, 'label'=>Mage::helper('mw_socialgift')->__('No, hide it')),            
            );        
        }
    }
?>