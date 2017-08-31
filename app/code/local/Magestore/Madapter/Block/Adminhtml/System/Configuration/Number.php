<?php

class Magestore_Madapter_Block_Adminhtml_System_Configuration_Number extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $count = Mage::getModel('madapter/device')->getCollection()->getSize();
        return $count;
    }

}
