<?php

class Magestore_Madapter_Model_Madapter extends Mage_Core_Model_Abstract {
            

    public function newZoozServer() {
        try {
            require_once Mage::getBaseDir('base') . DS . 'lib' . DS . 'Zooz' . DS . 'zooz.extended.server.api.php';
        } catch (Exception $e) {
            Mage::log($e);
        }
       $EMAIL = Mage::helper('madapter')->getConfigZooz('account');
       $API_ID = Mage::helper('madapter')->getConfigZooz('account_id');
       $zooz = new ZooZExtendedServerAPI($EMAIL, $API_ID, true);        
       $this->setData('zooz_server', $zooz);
       return $this->getData('zooz_server');
    }
    
    public function getTransactionDetailsById($id){
        $zooz = $this->getZoozServer();
        $detail = $zooz->getTransactionDetailsByTransactionID($id);
        return $detail;
    }
    
    public function cancelTransactionById($id){
        $zooz = $this->getZoozServer();
        $status = $zooz->cancelTransaction($id);
        return $status;
    }
    
    public function refundTransactionById($id){
        $zooz = $this->getZoozServer();
        $status = $zooz->refundTransaction($id);
        return $status;
    }
    
    public function commitTransactionById($id){
        $zooz = $this->getZoozServer();
        $status = $zooz->commitTransaction($id);
        return $status;
    }
}
