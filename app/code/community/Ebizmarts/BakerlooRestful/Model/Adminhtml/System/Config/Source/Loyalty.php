<?php

class Ebizmarts_BakerlooRestful_Model_Adminhtml_System_Config_Source_Loyalty {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => '', 'label' => ''),
            array('value' => 'Enterprise_Reward', 'label' => Mage::helper('adminhtml')->__('Magento Enterprise')),
            array('value' => 'TBT_Rewards', 'label' => Mage::helper('adminhtml')->__('Sweet Tooth')),
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