<?php

class Ebizmarts_BakerlooRestful_Model_V1_Api {

    public $parameters           = array();
    public $controllerName       = "";
    public $pageSize             = 50;
    public $defaultSort          = "updated_at";
    public $defaultDir           = "ASC";
    protected $_querySep         = ",";
    protected $_perPageLimit     = 400;
    protected $_storeId          = 1;
    protected $_model            = "core/config";
    protected $_outputAttributes = array();
    private $_filesMode          = false;
    protected $_router           = "brest";
    protected $_version          = 1;
    protected $_since            = true;

    /**
     * Collection object
     *
     * @var Varien_Data_Collection
     */
    protected $_collection = null;

    public function __construct($params) {
        $this->parameters = $params;

        //Set pageSize via QueryString, default is 50, max is 1000
        $limit = $this->_getQueryParameter('limit');
        if(!is_null($limit)) {

            if($limit <= $this->_perPageLimit) {
                $this->pageSize = (int)$limit;
            }
            else {
                $this->pageSize = $this->_perPageLimit;
            }

        }

        $dir = $this->_getQueryParameter('dir');
        if($dir && (strtoupper($dir) == "DESC" || strtoupper($dir) == "ASC") ) {
            $this->defaultDir = strtoupper($dir);
        }

        $sort = $this->_getQueryParameter('order');
        if($sort) {
            $this->defaultSort = (string)$sort;
        }

        $storeIdH = $this->_getRequestHeader(Mage::helper('bakerloo_restful')->getStoreIdHeader());
        if($storeIdH) {
            $this->setStoreId($storeIdH);

            //Apply StoreId
            $_store = Mage::app()->getStore($this->getStoreId());
            $_store->getId();
            Mage::app()->setCurrentStore($_store->getId());

        }
        else {
            $storeIdP = $this->_getQueryParameter('store_id');
            if($storeIdP) {
                $this->setStoreId($storeIdP);
            }
        }

        $since = $this->_getQueryParameter('since');
        //Return static files data
        if(!is_null($since) && 0 === intval($since)) {

            $filesModeByPass = Mage::helper('bakerloo_restful')->config('general/filesmode_bypass', $this->getStoreId());
            if(0 === (int)$filesModeByPass) {
                $this->setFilesMode(true);
            }

        }

    }

    /**
     * Process GET requests.
     *
     * @return array
     * @throws Exception
     */
    public function get() {

        $this->checkGetPermissions();

        $identifier = $this->_getIdentifier();

        if($identifier) { //get item by id

            if(is_numeric($identifier)) {
                return $this->_createDataObject((int)$identifier);
            }
            else {
                throw new Exception('Incorrect request');
            }

        }
        else {

            //get page
            $page = $this->_getQueryParameter('page');
            if(!$page) {
                $page = 1;
            }

            $filters     = $this->_getQueryParameter('filters');
            $resultArray = $this->_getAllItems($page, $filters);

            return $resultArray;

        }

    }

    public function put() {
        $this->checkPutPermissions();

        return $this;
    }

    public function post() {
        $this->checkPostPermissions();

        return $this;
    }

    public function delete() {
        $this->checkDeletePermissions();

        return $this;
    }

    protected function _getIdentifier() {
        $params = $this->parameters;
        $identifier = null;

        if(count($params)) {
            $values     = array_values($params);
            $identifier = (int)$values[0];
        }

        return $identifier;
    }

    protected function _getQueryParameter($key) {
        $params = $this->parameters;

        if(array_key_exists($key, $params)){
            return $params[$key];
        }
        else {
            return null;
        }
    }

    public function getFilesMode() {
        return $this->_filesMode;
    }

    public function setFilesMode($mode) {
        $this->_filesMode = $mode;
    }

    public function getStoreId() {
        return $this->_storeId;
    }

    public function setStoreId($id) {
        $this->_storeId = (int)$id;
    }

    public function getUsername() {
        return $this->_getRequestHeader(Mage::helper('bakerloo_restful')->getUsernameHeader());
    }

    public function getUsernameAuth() {
        return $this->_getRequestHeader(Mage::helper('bakerloo_restful')->getUsernameAuthHeader());
    }

    public function getDeviceId() {
        return $this->_getRequestHeader(Mage::helper('bakerloo_restful')->getDeviceIdHeader());
    }

    public function getUserAgent() {
        return $this->_getRequestHeader(Mage::helper('bakerloo_restful')->getUserAgentHeader());
    }

    public function getLatitude() {
        return $this->_getRequestHeader(Mage::helper('bakerloo_restful')->getLatitudeHeader());
    }

    public function getLongitude() {
        return $this->_getRequestHeader(Mage::helper('bakerloo_restful')->getLongitudeHeader());
    }

    protected function _getRequestHeader($header) {
        return $this->getRequest()->getHeader($header);
    }

    public function getRequest() {
        return Mage::app()->getRequest();
    }

    public function getStore() {
        $storeId = $this->getStoreId();
        return Mage::app()->getStore($storeId);
    }

    protected function _getCollectionPageObject($pageData, $pageNum, $prevPage, $nextPage, $count) {

        //@Fix: $count if $this->_totalPage is lower

        //next page link
        if($nextPage) {
            $nextUrl = $this->_reassembleRequestUrl();
            if(strpos($nextUrl,'?page=' . $pageNum)!==FALSE) {
                $nextUrl = str_replace('?page=' . $pageNum, '?page=' . $nextPage, $nextUrl);
            }elseif(strpos($nextUrl,'&page=' . $pageNum)!==FALSE) {
                $nextUrl = str_replace('&page=' . $pageNum, '?page=' . $nextPage, $nextUrl);
            }
            else {
                if(strpos($nextUrl,'?')===FALSE) {
                    $nextUrl .= "?page=" . $nextPage;
                }
                else {
                    $nextUrl .= "&page=" . $nextPage;
                }
            }
        }
        else {
            $nextUrl = null;
        }

        //prev page link
        if($prevPage) {
            $prevUrl = $this->_reassembleRequestUrl();
            if(strpos($prevUrl,'?page=' . $pageNum)!==FALSE){
                $prevUrl = str_replace('?page=' . $pageNum, '?page=' . $prevPage, $prevUrl);
            }elseif(strpos($prevUrl,'&page=' . $pageNum)!==FALSE){
                $prevUrl = str_replace('&page=' . $pageNum, '?page=' . $prevPage, $prevUrl);
            }
            else {
                if(strpos($prevUrl,'?')===FALSE) {
                    $prevUrl .= "?page=" . $prevPage;
                }
                else {
                    $prevUrl .= "&page=" . $prevPage;
                }
            }
        }
        else {
            $prevUrl = null;
        }

        $result = array(
                        'page_count'  => count($pageData),
                        'next_page'   => $nextUrl,
                        'prev_page'   => $prevUrl,
                        'total_count' => $count,
                        'total_pages' => ceil($count/$this->pageSize),
                        'page_data'   => $pageData
                       );

        return $result;

    }

    protected function _getCollection() {
        return Mage::getModel($this->_model)->getCollection();
    }

    protected function _getAllItems($page = 1, $filters = array()) {

        //Return static files data
        if($this->getFilesMode()) {
            return $this->_paginateCollection(null, $page);
        }

        $this->_collection = $this->_getCollection();

        $this->_collection->setOrder($this->defaultSort, $this->defaultDir);

        $this->_collection->setPageSize($this->pageSize)->setCurPage($page);

        if(is_array($filters) && !empty($filters)) {
            $this->_applyFilters($filters);
        }

        $since = $this->_getQueryParameter('since');

        if( $since && ($this->_since === true) ) {

            if(false === strpos($since, ":")) {
                $since = Mage::getModel('core/date')->date(null, $since);
            }

            if($this->_collection instanceof Mage_Core_Model_Mysql4_Collection_Abstract
                or $this->_collection instanceof Mage_Core_Model_Resource_Db_Collection_Abstract) {
                $this->_collection->addFieldToFilter($this->defaultSort, array("gt" => $since));
            }
            else {
                $this->_collection->addAttributeToFilter($this->defaultSort, array("gt" => $since));
            }

        }

        return $this->_paginateCollection($this->_collection->getSize(), $page);
    }

    /**
     * Applying array of filters to collection
     *
     * @param $filters
     */
    public function _applyFilters($filters) {

        foreach($filters as $_filter) {
            list($attributeCode, $condition, $value) = explode($this->_querySep, $_filter);

            if($this->_collection instanceof Mage_Core_Model_Mysql4_Collection_Abstract
                or $this->_collection instanceof Mage_Core_Model_Resource_Db_Collection_Abstract) {
                $this->_collection->addFieldToFilter($attributeCode, array($condition => $value));
            }
            else {
                $this->_collection->addAttributeToFilter($attributeCode, array($condition => $value));
            }

        }

    }

    protected function _paginateCollection($count, $page) {

        $resultArray = array();

        if($this->getFilesMode()) {

            $io = $this->_getIo($this->getResourceNameFromUrl());

            //Fetch export config data
            $_configData = $io->read("_pagedata.ser");

            $_config = unserialize($_configData);

            $totalPages = $_config['totalpages'];

            $_staticData = $io->read("page{$page}.ser");

            if(false === $_staticData) {
                Mage::throwException(Mage::helper('bakerloo_restful')->__('Page #%s not found.', $page));
            }

            $resultArray = unserialize($_staticData);

            $prevPage = null;
            if($page > 1) {
                $prevPage = $page - 1;
            }

            $nextPage = $page + 1;
            if($nextPage > $totalPages) {
                $nextPage = null;
            }

            //Set the items count
            $count = $_config['totalrecords'];

            $this->pageSize = $_config['perpage'];

        }
        else {

            $pageOutOfIndex = false;

            if(ceil($count/$this->pageSize) >= $page) {
                foreach ($this->_collection as $_item) {
                    $resultArray[] = $this->_createDataObject($_item->getId(), $_item);
                }
            }
            else {
                $pageOutOfIndex = true;
            }

            //prev page
            $prevPage=null;
            if($page>1){
                if($pageOutOfIndex){
                    $prevPage = floor($count/$this->pageSize);
                }else{
                    $prevPage = $page-1;
                }
            }

            //next page
            $nextPage=$page+1;
            if(ceil($count/$this->pageSize) < $nextPage) {
                $nextPage = null;
            }

        }

        return $this->_getCollectionPageObject($resultArray, $page, $prevPage, $nextPage, $count);
    }

    private function _reassembleRequestUrl() {

        //@ToDo: Change this to framework based query string

        $baseUrl = Mage::getUrl('*/*') . "index/";
        if(count($this->parameters)) {
            $keys = array_keys($this->parameters);

            //sort so page is first, if page is not first self::125 breaks :)
            if(isset($keys[1]) && $keys[1] != "page") {
                for ($i = 1; $i < count($keys); $i++) {
                    if("page" == $keys[$i]) {
                        $temp = $keys[1];
                        $keys[1] = $keys[$i];
                        $keys[$i] = $temp;
                        break;
                    }
                }
            }

            $name = $keys[0];
            $baseUrl .= $name . "/" . $this->parameters[$keys[0]];
            if(count($keys)>1){
                for($i=1;$i<count($keys);$i++){
                    if($i==1){
                        $baseUrl .= "?";
                    }else{
                        $baseUrl .= "&";
                    }
                    $baseUrl .= $keys[$i] . "=" . $this->parameters[$keys[$i]];
                }
            }
        }

        return $baseUrl;
    }

    public function _createDataObject($id = null, $data = null) {
        $result = null;

        if(is_null($data)) {
            $_item = Mage::getModel($this->_model)->load($id);
        }
        else {
            $_item = $data;
        }

        if($_item->getId()) {


            if(empty($this->_outputAttributes)) {
                $result = $_item->toArray();
            }
            else{
                $toAdd = array();

                /*foreach($this->_outputAttributes as $attributeCode => $attributeOutput) {
                    $toAdd[$attributeOutput] = $_item->getData($attributeCode);
                }*/
                foreach($this->_outputAttributes as $attributeCode) {
                    $toAdd[$attributeCode] = $_item->getData($attributeCode);
                }

                $result = $toAdd;
            }

        }

        return $result;
    }

    protected function _getIo($resource) {
        $path = Mage::helper('bakerloo_restful/cli')->getPathToDb($this->getStoreId(), $resource, false);

        try {
            $io = new Varien_Io_File();
            $io->open(array('path' => $path));
        }catch (Exception $ex) {
            return false;
        }

        return $io;
    }

    public function getResourceNameFromUrl() {
        $keys = array_keys($this->parameters);

        return $keys[0];
    }

    /**
     * Return helper instance.
     *
     * @return Ebizmarts_BakerlooRestful_Helper_Data
     */
    public function helper() {
        return Mage::helper('bakerloo_restful');
    }

    public function getApiPath(){
        //return "brest/v1_index/index/";
        return $this->_router . "/v" . $this->_version . "_index/index/";
    }

    public function getImagesPath(){
        return $this->_router . "/v" . $this->_version . "_catalog/image/";
    }

    /**
     * Validate user permissions for certain action.
     *
     * @param string $username
     * @param string $resource
     * @return boolean
     */
    public function isAllowed($resource, $username = null) {

        if(is_null($username)) {

            if($this->getUsernameAuth()) {
                $username = $this->getUsernameAuth();
            }
            else {
                $username = $this->getUsername();
            }
        }

        return (bool)Mage::helper('bakerloo_restful/acl')->isAllowed($username, $resource);
    }

    public function getJsonPayload() {
        $payload = $this->getRequest()->getRawBody();

        $data = json_decode($payload);

        if(!is_object($data)) {
            Mage::throwException("Invalid post data.");
        }

        return $data;
    }

    public function checkPermission(array $perms) {

        $allow = true;

        foreach($perms as $_perm) {
            $isUserAllowed = $this->isAllowed($_perm);
            if(!$isUserAllowed) {
                $allow = false;
                break;
            }
        }

        if(!$allow) {
            Mage::throwException("Not enough privileges or user is not active.");
        }

    }

    public function checkGetPermissions() {
        return $this;
    }

    public function checkPostPermissions() {
        return $this;
    }

    public function checkDeletePermissions() {
        return $this;
    }

    public function checkPutPermissions() {
        return $this;
    }

}