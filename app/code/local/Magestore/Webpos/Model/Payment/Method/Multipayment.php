<?php

/*
 * Web POS by Magestore.com
 * Version 2.3
 * Updated by Daniel - 12/2015
 */

class Magestore_Webpos_Model_Payment_Method_Multipayment extends Mage_Payment_Model_Method_Abstract {

    protected $_code = 'multipaymentforpos';
    protected $_infoBlockType = 'webpos/payment_method_multipayment_info_multipayment';
    protected $_formBlockType = 'webpos/payment_method_multipayment_multipaymentforpos';

    public function isAvailable($quote = null) {
        $routeName = Mage::app()->getRequest()->getRouteName();
        $multipaymentEnabled = Mage::helper('webpos/payment')->isMultiPaymentEnabled();
        if ($routeName == "webpos" && $multipaymentEnabled == true)
            return true;
        else
            return false;
    }

    public function assignData($data) {
        $info = $this->getInfoInstance();
        if ($data->getData('cashforpos_ref_no')) {
            $info->setData('cashforpos_ref_no', Mage::helper('core')->currency($data->getData('cashforpos_ref_no'), true, false));
        }
        if ($data->getData('ccforpos_ref_no')) {
            $info->setData('ccforpos_ref_no', Mage::helper('core')->currency($data->getData('ccforpos_ref_no'), true, false));
        }
        if ($data->getData('cp1forpos_ref_no')) {
            $info->setData('cp1forpos_ref_no', Mage::helper('core')->currency($data->getData('cp1forpos_ref_no'), true, false));
        }
        if ($data->getData('cp2forpos_ref_no')) {
            $info->setData('cp2forpos_ref_no', Mage::helper('core')->currency($data->getData('cp2forpos_ref_no'), true, false));
        }

        if ($data->getData('codforpos_ref_no')) {
            $info->setData('codforpos_ref_no', Mage::helper('core')->currency($data->getData('codforpos_ref_no'), true, false));
        }

        return $this;
    }

    public function validate() {
        parent::validate();
        $info = $this->getInfoInstance();
        /*
          if (!$info->getData('codforpos_ref_no')) {
          $errorCode = 'invalid_data';
          $errorMsg = $this->_getHelper()->__("Reference No is a required field.\n");
          }
          if ($errorMsg) {
          Mage::throwException($errorMsg);
          }
         */


        return $this;
    }

}
