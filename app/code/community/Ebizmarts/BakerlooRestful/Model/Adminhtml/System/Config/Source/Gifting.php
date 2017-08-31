<?php

class Ebizmarts_BakerlooRestful_Model_Adminhtml_System_Config_Source_Gifting {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => '', 'label' => ''),
            array('value' => 'Enterprise_GiftCard', 'label' => Mage::helper('adminhtml')->__('Magento Enterprise')),
            array('value' => 'AW_Giftcard', 'label' => Mage::helper('adminhtml')->__('aheadWorks Gift Card/Certificate')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray() {

        $result = array();

        foreach($this->toOptionArray() as $option) {
            $result[$option['value']] = $option['label'];
        }

        return $result;
    }

}