<?php

/* 
 * Web POS by Magestore.com
 * Version 2.3
 * Updated by Daniel - 12/2015
 */

class Magestore_Webpos_Model_Source_Adminhtml_Receiptfont {

    protected $_allowFonts = array();

    public function __construct() {
        $this->_allowFonts = array('monospace' => Mage::helper('webpos')->__('Monospace'), 'sans-serif'=> Mage::helper('webpos')->__('Sans-serif'));
    }

    public function toOptionArray() {
        if (!count($this->_allowFonts))
            return;

        $options = array();
        foreach ($this->_allowFonts as $value => $label) {
            $options[] = array('value' => $value, 'label' => $label);
        }

        return $options;
    }

    public function getAllowFonts() {
        return $this->_allowFonts;
    }

}