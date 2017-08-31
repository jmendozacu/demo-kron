<?php

class Magestore_Madapter_Model_Transfer extends Mage_Payment_Model_Method_Abstract {

    protected $_code = 'transfer_mobile';
       protected $_infoBlockType = 'madapter/transfer';
   // protected $_formBlockType = 'madapter/form_banktransfer';
//    protected $_infoBlockType = 'madapter/info_banktransfer';

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions() {              
        return trim($this->getConfigData('instructions'));
    }

}

