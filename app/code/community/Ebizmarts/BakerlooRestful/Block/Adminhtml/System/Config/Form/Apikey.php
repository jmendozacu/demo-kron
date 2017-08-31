<?php

class Ebizmarts_BakerlooRestful_Block_Adminhtml_System_Config_Form_Apikey extends Mage_Adminhtml_Block_System_Config_Form_Field {

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $element->setReadonly(true);

        return parent::render($element);
    }

}