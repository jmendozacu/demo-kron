<?php
class Vivacity_Locator_Block_Adminhtml_Entity_Helper_Image extends Varien_Data_Form_Element_Image{
    protected function _getUrl(){
        $url = false;
        if ($this->getValue()) {
            $url =  Mage::getBaseUrl('media') . 'storeLocator/'.$this->getValue();
        }
        return $url;
    }
}
