<?php

class Magestore_Madapter_Model_System_Config_Product {

    public function toOptionArray() {
        return array(
            array('value' => '1', 'label' => Mage::helper('madapter')->__('Products Best Seller')),
            array('value' => '2', 'label' => Mage::helper('madapter')->__('Products Most View')),
            array('value' => '3', 'label' => Mage::helper('madapter')->__('Products New Update')),
            array('value' => '4', 'label' => Mage::helper('madapter')->__('Products Recently Added')));
    }

}