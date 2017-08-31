<?php

class Simi_ztheme_Model_Config {

    public function toOptionArray() {
        return array(
            array('value' => '1', 'label' => Mage::helper('core')->__('Best Seller')),
            array('value' => '2', 'label' => Mage::helper('core')->__('Most View')),
            array('value' => '3', 'label' => Mage::helper('core')->__('New Update')),
            array('value' => '4', 'label' => Mage::helper('core')->__('Recently Added')),
            array('value' => '5', 'label' => Mage::helper('core')->__('Feature')));
    }
    
    public function toKeySpotArray(){
        return array("best_seller","most_view","new_update","recent_add","feature");
    }

}