<?php

/*
 * Web POS by Magestore.com
 * Version 2.3
 * Updated by Daniel - 12/2015
 */

class Magestore_Webpos_Model_Payment_Method_Cod extends Mage_Payment_Model_Method_Abstract {
    /* This model define payment method */

    protected $_code = 'codforpos';
    protected $_infoBlockType = 'webpos/payment_method_cod_info_cod';
    protected $_formBlockType = 'webpos/payment_method_cod_codforpos';

    public function isAvailable($quote = null) {
        $routeName = Mage::app()->getRequest()->getRouteName();
        $codenabled = Mage::helper('webpos/payment')->isCodPaymentEnabled();
        if ($routeName == "webpos" && $codenabled == true)
            return true;
        else
            return false;
    }

    public function assignData($data) {
        $info = $this->getInfoInstance();
        if ($data->getData('codforpos_ref_no')) {
            $info->setData('codforpos_ref_no', $data->getData('codforpos_ref_no'));
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
