<?php

class Ebizmarts_BakerlooRestful_Helper_Data extends Mage_Core_Helper_Abstract {


    private $EncryptationKey              = "gda7asvdsa76gd7a";
    private $EncryptationIV               = "0d6Hs4L1opAqwte8";
    private $ActivationKeyExpirationHours = 24;

    public function getActivationKeyExpirationHours() {
        return $this->ActivationKeyExpirationHours;
    }

    public function getResizedImageUrl($productId, $storeId, $imagePath, $width, $height, $categoryId = null) {

        $imagesControllerPath = Mage::getModel('bakerloo_restful/api_api')->getImagesPath();

        //Mage_Core_Helper_Data
        $ch = Mage::helper('core/url');

        $params = array(
            'f' => $ch->urlEncode($imagePath),
            'w' => $width,
            'h' => $height
        );

        if(is_null($categoryId)) {
            $params['p'] = $ch->urlEncode($productId);
        }
        else {
            $params['c'] = $ch->urlEncode($categoryId);
        }

        $url = Mage::getModel('core/store')->load($storeId)->getUrl($imagesControllerPath, $params);

        return $url;
    }

    public function getStoreIdHeader() {
        return 'B-Store-Id';
    }

    public function getApiKeyHeader() {
        return 'B-Api-Key';
    }

    public function getActivationKeyHeader() {
        return 'B-Activation-Key';
    }

    public function getUsernameHeader() {
        return 'B-Username';
    }

    public function getUsernameAuthHeader() {
        return 'B-Username-Auth';
    }

    public function getDeviceIdHeader() {
        return 'B-Device-Id';
    }

    public function getUserAgentHeader() {
        return 'B-User-Agent';
    }

    public function getLatitudeHeader() {
        return 'B-Latitude';
    }

    public function getLongitudeHeader() {
        return 'B-Longitude';
    }

    public function getApiVersionHeader() {
        return 'B-Api-Version';
    }

    public function getMagentoVersionHeader() {
        return 'B-Magento-Version';
    }

    public function getMagentoVersionCode() {
        $modules = (array) Mage::getConfig()->getNode('modules')->children();
        $flavour = (array_key_exists('Enterprise_Enterprise', $modules)) ? 'EE' : 'CE';

        return $flavour;
    }

    public function allPossibleHeaders() {
        return array(
                     'B-Store-Id', 'B-Api-Key', 'B-Username',
                     'B-Username-Auth', 'B-Device-Id', 'B-User-Agent',
                     'B-Latitude', 'B-Longitude', 'B-Api-Version'
        );
    }

    public function getApiModuleVersion() {
        return (string)Mage::getConfig()->getNode('modules/Ebizmarts_BakerlooRestful/version');
    }

    public function getUserAgent() {
        $v = $this->getApiModuleVersion();
        return "Ebizmarts/BakerlooRestful (v{$v})";
    }

    public function getApiKey($storeId = null) {
        return Mage::helper('core')->decrypt($this->config("general/api_key", $storeId));
    }

    public function getActivationKey($websiteId = null) {
        return Mage::helper('core')->decrypt($this->config("general/activation_key", $websiteId));
    }

    public function apiGenUrl() {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/bakerloo/generatekey', array('_secure' => true));
    }

    public function activationGenUrl() {
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/bakerloo/generateactivationkey', array('_secure' => true));
    }

    public function config($path, $storeId = null) {
        return Mage::getStoreConfig("bakerloorestful/$path", $storeId);
    }

    public function logprofiler($action) {
        $suiteLogPath = Mage::getBaseDir('var') . DS . 'log' . DS . 'PosAPI';
        $profilerPath = $suiteLogPath . DS . 'PROFILER';

        if (!is_dir($suiteLogPath)) {
            mkdir($suiteLogPath, 0777);
        }
        if (!is_dir($profilerPath)) {
            mkdir($profilerPath, 0777);
        }

        $timers = Varien_Profiler::getTimers();

        $request = $action->getRequest();
        $prefix = $request->getParam('vtxcode', $request->getParam('VPSTxId', null));
        $prefix = ($prefix ? $prefix . '_' : '');

        $longest = 0;
        $rows = array();
        foreach ($timers as $name => $timer) {

            $sum = Varien_Profiler::fetch($name, 'sum');
            $count = Varien_Profiler::fetch($name, 'count');
            $realmem = Varien_Profiler::fetch($name, 'realmem');
            $emalloc = Varien_Profiler::fetch($name, 'emalloc');
            if ($sum < .0010 && $count < 10 && $emalloc < 10000) {
                continue;
            }

            $rows [] = array((string) $name, (string) number_format($sum, 4), (string) $count, (string) number_format($emalloc), (string) number_format($realmem));
            $thislong = strlen($name);
            if ($thislong > $longest) {
                $longest = $thislong;
            }
        }

        //Create table
        $table = new Zend_Text_Table(array('columnWidths' => array($longest, 10, 6, 12, 12), 'decorator' => 'ascii'));

        //Memory
        $preheader = new Zend_Text_Table_Row();
        $real = memory_get_usage(true);
        $emalloc = memory_get_usage();
        $preheader->appendColumn(new Zend_Text_Table_Column('real Memory usage: ' . $real . ' ' . ceil($real / 1048576) . 'MB', 'center', 1));
        $preheader->appendColumn(new Zend_Text_Table_Column('emalloc Memory usage: ' . $emalloc . ' ' . ceil($emalloc / 1048576) . 'MB', 'center', 4));
        $table->appendRow($preheader);

        //Append Header
        $header = new Zend_Text_Table_Row();
        $header->appendColumn(new Zend_Text_Table_Column('Code Profiler', 'center'));
        $header->appendColumn(new Zend_Text_Table_Column('Time', 'center'));
        $header->appendColumn(new Zend_Text_Table_Column('Cnt', 'center'));
        $header->appendColumn(new Zend_Text_Table_Column('Emalloc', 'center'));
        $header->appendColumn(new Zend_Text_Table_Column('RealMem', 'center'));
        $table->appendRow($header);

        foreach ($rows as $row) {
            $table->appendRow($row);
        }

        //SQL profile
        $dbprofile = print_r(Varien_Profiler::getSqlProfiler(Mage::getSingleton('core/resource')->getConnection('core_write')), TRUE);
        $dbprofile = substr($dbprofile, 0, -4);
        $dbprofile = str_replace('<br>', "\n", $dbprofile);

        $preheaderlabel = new Zend_Text_Table_Row();
        $preheaderlabel->appendColumn(new Zend_Text_Table_Column('DATABASE', 'center', 5));
        $table->appendRow($preheaderlabel);
        $preheader = new Zend_Text_Table_Row();
        $preheader->appendColumn(new Zend_Text_Table_Column($dbprofile, 'left', 5));
        $table->appendRow($preheader);

        //Request
        $rqlabel = new Zend_Text_Table_Row();
        $rqlabel->appendColumn(new Zend_Text_Table_Column('REQUEST', 'center', 5));
        $table->appendRow($rqlabel);
        $inforqp = new Zend_Text_Table_Row();
        $inforqp->appendColumn(new Zend_Text_Table_Column(print_r($request, TRUE), 'left', 5));
        $table->appendRow($inforqp);

        $date = Mage::getModel('core/date')->date('Y-m-d\.H-i-s');

        $file = new SplFileObject($profilerPath . DS . $prefix . $date . '_' . $action->getFullActionName() . '.txt', 'w');
        $file->fwrite($table);
    }

    public function validateMysqlTimestamp($string) {
        return preg_match('/^\d{4}(-)\d{2}(-)\d{2}\s{1}\d{2}(:)\d{2}(:)\d{2}$/', $string) === 1;
    }

    public function encryptActivationKey($data) {

        $blocksize = 16; // AES-128
        $pad = $blocksize - (strlen($data) % $blocksize);
        $data = $data . str_repeat(chr($pad), $pad);
        return bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->EncryptationKey, $data, MCRYPT_MODE_CBC, $this->EncryptationIV));
    }

    public function decryptActivationKey($data) {

        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->EncryptationKey, pack('H*', $data), MCRYPT_MODE_CBC, $this->EncryptationIV);
        //$block = 16;
        $pad = ord($decrypted[($len = strlen($decrypted)) - 1]);
        $decrypted = substr($decrypted, 0, strlen($decrypted) - $pad);
        return $decrypted;

    }

    public function debug($object) {

        if((int)$this->config("general/debug") === 0) {
            return;
        }

        $debugId = (int)Mage::registry('brest_request_id');

        $debug = Mage::getModel('bakerloo_restful/debug');

        if($debugId) {
            $debug->load($debugId);
        }

        if($object instanceof Mage_Core_Controller_Response_Http) {
            $debug
            ->setResponseHeaders(json_encode($object->getHeaders()))
            ->setResponseBody(json_encode($object->getBody()))
            ->save();
        }
        else {

            //Mage_Core_Controller_Request_Http

            $debug->setRequestMethod($object->getMethod());

            switch($object->getMethod()) {
                case 'GET':
                case 'DELETE':
                    $debug->setRequestBody(json_encode($object->getParams()));
                break;
                case 'PUT':
                case 'POST':
                    $debug->setRequestBody(json_encode($object->getRawBody()));
                    break;
            }

            $headers = $this->allPossibleHeaders();
            $saveHeaders = array();
            foreach($headers as $_header) {
                $saveHeaders[$_header] = $object->getHeader($_header);
            }
            $debug->setRequestHeaders(serialize($saveHeaders));

            $debug->save();
        }

        return $debug->getId();

    }

    public function createCustomer($websiteId, $data, $password = null) {

        if(is_null($password)) {
            $password = substr(uniqid(), 0, 8);
        }

        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId($websiteId);

        //Create customer
        $customer->setPassword($password);
        $customer->setConfirmation($password);
        $customer->setId(null);

        if(!isset($data->customer->group_id))
            Mage::throwException($this->__("Please provide Customer Group ID."));
        else
            $customer->setGroupId((int)$data->customer->group_id);

        $customer->setEmail((string)$data->customer->email);
        $customer->setFirstname((string)$data->customer->firstname);
        $customer->setLastname((string)$data->customer->lastname);

        //Subscribe customer to newsletter on creation
        if (isset($data->customer->subscribed_to_newsletter) and ((bool)$data->customer->subscribed_to_newsletter) === true) {
            $customer->setIsSubscribed(1);
        }

        $customer->save();

        //Send welcome email if enabled in config.
        Mage::helper('bakerloo_restful/email')->sendWelcome($customer);

        return $customer;
    }

    public function jsonError($message) {
        return array("error" => array("message" => $message));
    }

    public function encodeResponse($data) {
        return json_encode($data);
    }

    public function isCallAllowed($request, $storeId = null, $apiKeyOverride = null) {
        $allow = true;

        $allowedIps = $this->config("general/allow_ips", $storeId);
        $remoteAddr = Mage::helper('core/http')->getRemoteAddr();
        if (!empty($allowedIps) && !empty($remoteAddr)) {
            $allowedIps = preg_split('#\s*,\s*#', $allowedIps, null, PREG_SPLIT_NO_EMPTY);
            if (array_search($remoteAddr, $allowedIps) === false
                && array_search(Mage::helper('core/http')->getHttpHost(), $allowedIps) === false) {

                Mage::throwException("API access denied: Invalid IP.");

            }
        }

        //Validate API Header if IP validated
        if(true === $allow) {

            if(is_null($apiKeyOverride)) {
                $apiKey = $request->getHeader($this->getApiKeyHeader());
            }
            else {
                $apiKey = $apiKeyOverride;
            }

            if((false === $apiKey) or ($this->getApiKey() != $apiKey)) {
                Mage::throwException("API access denied: Invalid API key.");
            }

        }

        return $allow;
    }

    public function isModuleInstalled($moduleName) {
        return Mage::getConfig()->getNode("modules/{$moduleName}");
    }

    /**
     * Notifications severity options
     */
    public function getSeverityOptions() {
        return array(
                    1 => Mage::helper('bakerloo_restful')->__('CRITICAL'),
                    2 => Mage::helper('bakerloo_restful')->__('MAJOR'),
                    3 => Mage::helper('bakerloo_restful')->__('MINOR'),
                    4 => Mage::helper('bakerloo_restful')->__('NOTICE')
        );
    }

    public function getSeverityOption($id) {
        $options = $this->getSeverityOptions();

        return (isset($options[$id]) ? $options[$id] : null);
    }

    public function getMagentoDomain() {
        $useStoreCode = (int)Mage::getStoreConfig('web/url/use_store');

        $storeId = 0;

        if($useStoreCode) {
            $websites     = Mage::app()->getWebsites(false);
            $firstWebsite = current($websites);
        }
        else {
            $websites     = Mage::app()->getWebsites(true, 'admin');
            $firstWebsite = $websites['admin'];
        }

        foreach($firstWebsite->getStores() as $_store) {

            if($_store->getIsActive()) {
                $storeId = $_store->getStoreId();
                break;
            }

        }

        $magentoDomain = Mage::getModel('core/url')->setStore($storeId)->getUrl("/", array('_nosid' => true));

        return $magentoDomain;
    }

    public function getStoreAddress($storeId) {
        $store = Mage::getModel('core/store')->load($storeId);

        $address = array();

        $address['address_street'] = $store->getConfig('general/store_information/address');
        $address['country']        = (string)$store->getConfig('general/country/default');
        $address['postal_code']    = $store->getConfig('general/store_information/postal_code');
        $address['region_id']      = $store->getConfig('general/store_information/region_id');
        $address['telephone']      = $store->getConfig('general/store_information/phone');

        return $address;
    }

    /**
     * Return config value for "Import orders regardless of stock level"
     *
     * @param null $storeId
     * @return bool
     */
    public function dontCheckStock($storeId = null) {
        return ((int)$this->config("catalog/always_in_stock", $storeId) === 1);
    }

    /**
     * Return config value for "Import orders regardless of stock level"
     *
     * @param null $storeId
     * @return bool
     */
    public function dontSubtractInventory($storeId = null) {
        return ((int)$this->config("catalog/subtract_inventory", $storeId) === 1);
    }

    public function encryptOrderBackupFile($data) {

        $blocksize = 16; // AES-128
        $pad = $blocksize - (strlen($data) % $blocksize);
        $data = $data . str_repeat(chr($pad), $pad);
        return bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->EncryptationKey, $data, MCRYPT_MODE_CBC, $this->EncryptationIV));
    }

    public function decryptOrderBackupFile($data) {

        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->EncryptationKey, pack('H*', $data), MCRYPT_MODE_CBC, $this->EncryptationIV);
        //$block = 16;
        $pad = ord($decrypted[($len = strlen($decrypted)) - 1]);
        $decrypted = substr($decrypted, 0, strlen($decrypted) - $pad);
        return $decrypted;

    }

    public function getProductBarcode($productId, $storeId = null) {

        $product = Mage::getModel('catalog/product')
                    ->setStoreId($storeId)
                    ->load($productId);

        $locatorAttribute = (string) Mage::helper('bakerloo_restful')->config('catalog/product_code', $storeId);

        $ret = '';

        if( strpos($locatorAttribute, ',') === false ) {
            $ret = $product->getData($locatorAttribute);
        }
        else {

            $temp = array();
            $attributes = explode(',', $locatorAttribute);

            for ($i = 0; $i < count($attributes); $i++) {
                $tempVal = $product->getData($attributes[$i]);

                if( empty($tempVal) )
                    continue;

                array_push($temp, $tempVal);
            }

            $ret = implode(',', $temp);

        }

        return $ret;

    }

}