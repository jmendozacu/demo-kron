<?php

/*
 * Web POS by Magestore.com
 * Version 2.3
 * Updated by Daniel - 12/2015
 */

class Magestore_Webpos_Block_Payment_Method_Multipayment_Info_Multipayment extends Mage_Payment_Block_Info {
    /*
      This block will show the payment method information
     */

    protected function _prepareSpecificInformation($transport = null) {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $data = array();
        if ($this->getInfo()->getData('cashforpos_ref_no')) {
            $data[Mage::helper('webpos/payment')->getCashMethodTitle()] = strip_tags($this->getInfo()->getData('cashforpos_ref_no'));
        }
        if ($this->getInfo()->getData('ccforpos_ref_no')) {
            $data[Mage::helper('webpos/payment')->getCcMethodTitle()] = strip_tags($this->getInfo()->getData('ccforpos_ref_no'));
        }
        if ($this->getInfo()->getData('cp1forpos_ref_no')) {
            $data[Mage::helper('webpos/payment')->getCp1MethodTitle()] = strip_tags($this->getInfo()->getData('cp1forpos_ref_no'));
        }
        if ($this->getInfo()->getData('cp2forpos_ref_no')) {
            $data[Mage::helper('webpos/payment')->getCp2MethodTitle()] = strip_tags($this->getInfo()->getData('cp2forpos_ref_no'));
        }
        if ($this->getInfo()->getData('codforpos_ref_no')) {
            $data[Mage::helper('webpos/payment')->getCodMethodTitle()] = strip_tags($this->getInfo()->getData('codforpos_ref_no'));
        }
        
        $transport = parent::_prepareSpecificInformation($transport);
        return $transport->setData(array_merge($data, $transport->getData()));
    }

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('webpos/webpos/payment/method/info/multipaymentforpos.phtml');
    }

    public function getMethodTitle() {
        return Mage::helper('webpos/payment')->getMultipaymentMethodTitle();
    }

}
