<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Ebizmarts_BakerlooRestful_V1_IndexController extends Mage_Core_Controller_Front_Action {

    public $verb = null;
    public $parameters = array();

    public function indexAction() {

        $id = Mage::helper('bakerloo_restful')->debug($this->getRequest());
        Mage::register('brest_request_id', $id);

        $this->getResponse()
             ->setHeader('Content-Type', 'application/json', true)
             ->setHeader('Connection', 'keep-alive', true);

        //Check if module is active
        if(!$this->_isEnabled()) {
            return $this->getResponse()
                ->setHttpResponseCode(410)
                ->setBody($this->encodeResponse($this->_error("Service is not active.")));
        }

        //Check if activation request
        if($this->_isActivationRequest()) {
            $activationData = $this->_getActivateAccountData();
            return $this->getResponse()
                ->setHttpResponseCode(200)
                ->setBody($this->encodeResponse($activationData));
        }

        //Validate Request
        try{

            $this->_isCallAllowed();

        } catch(Mage_Core_Exception $ce) {
            return $this->getResponse()
                ->setHttpResponseCode(401)
                ->setBody($this->encodeResponse($this->_error($ce->getMessage())));
        }

        //Set API Key Header
        $this->getResponse()
             ->setHeader(Mage::helper('bakerloo_restful')->getApiKeyHeader(), $this->_getApiKey(), true);

        try {

            $this->verb = $this->getRequest()->getMethod();
            $this->parameters = $this->getRequest()->getParams();

            try {
                $className = 'bakerloo_restful/V1_' . $this->_getControllerName();

                $model = Mage::getModel($className, $this->parameters);
            }catch(Mage_Core_Model_Store_Exception $e) {
                Mage::logException($e);
                Mage::throwException("Invalid Store.");
            }catch(Exception $e) {
                Mage::logException($e);
                Mage::throwException("Resource not found.");
            }

            //GET, POST, PUT, DELETE...
            $actionName = strtolower($this->verb);

            //Allow custom actions to be used eg ?action=sendEmail
            //When using custom actions, HTTP VERB does not matter
            $customAction = $this->getRequest()->getParam('action', null);
            if(!empty($customAction)) {
                $actionName = $customAction;
            }

            if(!method_exists($model, $actionName)) {
                Mage::throwException("Invalid action.");
            }

            //Execute action
            $result = $model->$actionName();

            $this->getResponse()
                 ->setHttpResponseCode(200)
                 ->setBody($this->encodeResponse($result));

            if($actionName == "post") {
                $this->getResponse()
                ->setHttpResponseCode(201);
            }

        }catch(Exception $ex) {

            Mage::logException($ex);

            $this->getResponse()
                 ->setHttpResponseCode(500)
                 ->setBody($this->encodeResponse($this->_error($ex->getMessage())));

        }

    }

    private function _getControllerName() {

        $params = $this->parameters;
        $name = "";

        if(count($params)) {
            $keys = array_keys($params);
            $name = $keys[0];
        }

        return $name;

    }

    public function encodeResponse($data) {
        return json_encode($data);
    }

    private function _error($message) {
        return array("error"=>array("message" => $message));
    }

    /**
     * Validate request IP address checking against whitelist in config.
     *
     * @param type $storeId
     * @return boolean
     */
    private function _isCallAllowed($storeId = null) {

        $allow = true;

        $allowedIps = Mage::helper("bakerloo_restful")->config("general/allow_ips", $storeId);
        $remoteAddr = Mage::helper('core/http')->getRemoteAddr();
        if (!empty($allowedIps) && !empty($remoteAddr)) {
            $allowedIps = preg_split('#\s*,\s*#', $allowedIps, null, PREG_SPLIT_NO_EMPTY);
            if (array_search($remoteAddr, $allowedIps) === false
                && array_search(Mage::helper('core/http')->getHttpHost(), $allowedIps) === false) {

                Mage::throwException("Bakerloo api access denied: Invalid IP.");

            }
        }
        else {
            //@ToDo.
        }

        //Validate API Header if IP validated
        if(true === $allow) {

            $apiKey = $this->getRequest()->getHeader(Mage::helper('bakerloo_restful')->getApiKeyHeader());

            if((false === $apiKey) or ($this->_getApiKey() != $apiKey)) {

                Mage::throwException("Bakerloo api access denied: Invalid API key.");
            }
            /*else {
                $storeId = $this->getRequest()->getHeader(Mage::helper('bakerloo_restful')->getStoreIdHeader());

                if(false === $storeId) {
                   $allow = false;
                }
            }*/
        }

        return $allow;
    }

    private function _isActivationRequest(){

        $activationKey = $this->getRequest()->getHeader(Mage::helper('bakerloo_restful')->getActivationKeyHeader());

        if($activationKey && $activationKey!=''){
            return true;
        }else{
            return false;
        }


    }

    private function _getActivateAccountData() {

        $activationKey = $this->getRequest()->getHeader(Mage::helper('bakerloo_restful')->getActivationKeyHeader());

        $decriptedKeyData = Mage::helper('bakerloo_restful')->decryptActivationKey($activationKey);

        //activation key syntax is URL|DATE_CREATED
        $pieces = explode("|", $decriptedKeyData);
        if(count($pieces) == 3) {
            $url           = $pieces[0];
            $date          = $pieces[2];
            $magentoDomain = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
            $currDate      = date("Y-m-d");
            $hourdiff      = round((strtotime($currDate) - strtotime($date))/3600, 1);

            if($url == $magentoDomain) {
                if($hourdiff < Mage::helper('bakerloo_restful')->getActivationKeyExpirationHours()) {
                    return $this->encodeResponse(array("api_key" => $this->_getApiKey(), "shop_type" => "magento"));
                }
                else {
                    return $this->encodeResponse(array("error" => "Activation key expired"));
                }
            }
            else {
                return $this->encodeResponse(array("error" => "Incorrect activation key"));
            }

        }
        else {
            return $this->encodeResponse(array("error" => "Incorrect activation key"));
        }

    }

    /**
     * Check if the module is active.
     *
     */
    private function _isEnabled() {
        return (boolean)Mage::helper("bakerloo_restful")->config("general/enabled");
    }

    private function _getApiKey() {
        return Mage::helper('bakerloo_restful')->getApiKey();
    }

}