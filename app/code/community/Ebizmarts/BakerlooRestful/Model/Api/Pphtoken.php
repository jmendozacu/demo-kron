<?php

class Ebizmarts_BakerlooRestful_Model_Api_Pphtoken extends Ebizmarts_BakerlooRestful_Model_Api_Api {

    private $_backend_reactivate_url = "https://pos.ebizmarts.com/admin/paypal-here-reactivate";

    protected $_configPath = 'payment/bakerloo_paypalhere/';

    /**
     * Process GET requests.
     *
     * @return type
     * @throws Exception
     */
    public function get() {

        $this->checkGetPermissions();

        if(!$this->getStoreId()) {
            Mage::throwException('Please provide a Store ID.');
        }

        $store = $this->getStoreId();

        $mode = $this->_getQueryParameter('mode');
        if($mode == null || $mode == ''){
            $mode = Mage::getStoreConfig($this->_configPath . 'api_mode', $store);
        }
        //Mage::log($mode);

        $timestamp = Mage::getStoreConfig($this->_configPath . 'timestamp_' . $mode, $store);
        $access_token = Mage::getStoreConfig($this->_configPath . 'access_token_' . $mode, $store);
        $refresh_token = Mage::getStoreConfig($this->_configPath . 'refresh_token_' . $mode, $store);
        $backend_account_id = Mage::getStoreConfig($this->_configPath . 'backend_account_id_' . $mode, $store);
        $nowTimestamp = (time()*1000) - 600000; //add 10 min window

        //Mage::log($timestamp);
        //Mage::log($nowTimestamp);

        if($access_token && $access_token != ''){

            if($nowTimestamp > $timestamp){
                //request new token
                $data = array("refresh_token"=>$refresh_token,"backend_account_id"=>$backend_account_id,"store"=>$store, "mode"=>$mode);
                $headers = array();
                $response = Mage::helper('bakerloo_restful/http')->POST($this->_backend_reactivate_url, $data, $headers);
                $objResponse = json_decode($response);
                if($objResponse->error && $objResponse->error != ''){
                    Mage::throwException("Access token is old, refresh failed: " . $objResponse->error);
                }else{
                    $coreConfig = new Mage_Core_Model_Config();
                    $coreConfig ->saveConfig($this->_configPath . 'access_token_' . $mode, $objResponse->access_token, 'default', $store);
                    $access_token = $objResponse->access_token;
                    $coreConfig ->saveConfig($this->_configPath . 'timestamp_' . $mode, $objResponse->timestamp, 'default', $store);
                    $timestamp = $objResponse->timestamp;
                    Mage::getConfig()->reinit();
                }
            }

            $resultArray = array("access_token" => $access_token,
                "timestamp" => $timestamp);

            return $resultArray;
        }else{
            Mage::throwException('Access token was not generated for ' . $mode . ' mode.');
        }
    }

    /**
     * save new token
     *
     */
    public function post() {

        parent::post();

        if(!$this->getStoreId()) {
            Mage::throwException('Please provide a Store ID.');
        }
        $store = $this->getStoreId();

        $data = $this->getJsonPayload();

        $coreConfig = new Mage_Core_Model_Config();
        $coreConfig ->saveConfig($this->_configPath . 'access_token_' . $data->mode, $data->access_token, 'default', $store);
        $coreConfig ->saveConfig($this->_configPath . 'refresh_token_' . $data->mode, $data->refresh_token, 'default', $store);
        $coreConfig ->saveConfig($this->_configPath . 'backend_account_id_' . $data->mode, $data->backend_account_id, 'default', $store);
        $coreConfig ->saveConfig($this->_configPath . 'timestamp_' . $data->mode, $data->timestamp, 'default', $store);

        Mage::getConfig()->reinit();

        return $this;

    }

}