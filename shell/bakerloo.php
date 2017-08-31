<?php

ini_set('display_errors', 1);

ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');

require_once 'abstract.php';

/**
 * Bakerloo Shell DB static generator
 *
 * @category    Ebizmarts
 * @package     Ebizmarts_BakerlooShell
 * @author      Ebizmarts Team <info@ebizmarts.com>
 */
class Ebizmarts_BakerlooShell_Bakerloo extends Mage_Shell_Abstract {

    private $_resources = array(
        'categories',
        'products',
        'inventory',
        'customers',
    );

    private $_storeId  = null;
    private $_pageSize = 200;
    private $_debug    = false;
    private $_startPage = 2;
    private $_io = null;

    public function getStoreId() {
        return $this->_storeId;
    }

    public function setStoreId($id = 0) {
        $this->_storeId = $id;
    }

    /**
     * Run script
     *
     */
    public function run() {

        $this->_debug = (boolean)$this->getArg('trace');

        $this->setStoreId((int)$this->getArg('storeid'));

        if(!$this->getStoreId()) {
            die("Please provice a store ID. See help for more information.\n");
        }

        //Validate storeid
        try {

            $_store = Mage::app()->getStore($this->getStoreId());
            $_store->getId();

            Mage::app()->setCurrentStore($_store->getId());

        } catch(Exception $ex) {
            die("Store is not valid.\n");
        }

        $pageSizeP = (int)$this->getArg('pagesize');
        if($pageSizeP) {
            $this->_pageSize = $pageSizeP;
        }

        $startPage = (int)$this->getArg('startpage');
        if($startPage) {
            $this->_startPage = $startPage;
        }

        $this->trace("++++++++++ Processing entities for Store: " . $_store->getName() . " ++++++++++\n\n", true);

        if($this->getArg('all')) {

            for ($i = 0; $i < count($this->_resources); $i++) {
                $resource = $this->_resources[$i];
                $this->processResource($resource);
                $this->_reset();
            }

        }
        else {

            $resource = $this->getArg('resource');

            if($resource && in_array($resource, $this->_resources)) {
                $this->processResource($resource);
            }
            else {
                echo $this->usageHelp();
            }

        }

    }

    private function processResource($resource) {

        $this->trace("********** >>> Processing {$resource} " . Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s') . " **********\n\n");

        if($this->_startPage === 2) {
            //Save first page
            $pageNumber = 1;

            $this->trace("START: Processing page {$pageNumber} " . Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s') . "\n");

            $data = $this->getData($resource, null, $this->_pageSize, $pageNumber);

            $this->storeData($resource, $data, $this->getStoreId(), $pageNumber);

            $this->trace("FINISH: Processing page {$pageNumber} " . Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s') . "\n\n");
        }
        else {
            $data = array();
            $data['next_page'] = $this->_startPage - 1;
            $pageNumber = $this->_startPage - 1;
        }

        $ioAdapter = $this->getIo($this->getStoreId(), $resource, false);

        //Save from page 2 to page n
        while(!is_null($data['next_page'])) {
            $pageNumber++;

            $this->trace("START: Processing page {$pageNumber} " . Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s') . "\n");

            $data = $this->getData($resource, null, $this->_pageSize, $pageNumber);

            $ioAdapter->write("page{$pageNumber}.ser", serialize($data['page_data']));

            $this->trace("FINISH: Processing page {$pageNumber} " . Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s') . "\n\n");

        }

        $this->trace("********** Finished {$resource} " . Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s') . " <<< **********\n\n\n", true);

    }

    private function storeData($resource, array $data, $storeId, $pageNumber) {

        //Path to file: For example, /path/to/magento/var/bakerloo/1/products
        $reset = false;

        if(1 === $pageNumber) {
            $reset = true;
        }

        $ioAdapter = $this->getIo($storeId, $resource, $reset);

        $_data = array();

        if(!isset($data['page_data'])) {
            $_data = $data;
        }
        else {
            $_data = $data['page_data'];
        }

        //Save export global information
        if(1 === $pageNumber) {
            $ioAdapter->write("_pagedata.ser", $this->_getExportConfig($data));
        }

        $ioAdapter->write("page{$pageNumber}.ser", serialize($_data));

        unset($data);
    }

    private function getIo($storeId, $resource, $reset) {

        if(is_null($this->_io)) {
            $path = Mage::helper('bakerloo_restful/cli')->getPathToDb($storeId, $resource, $reset);
            $ioAdapter = new Varien_Io_File();
            $ioAdapter->open(array('path' => $path));
            $this->_io = $ioAdapter;
        }

        return $this->_io;
    }

    private function getData($resource, $since, $limit, $page = 1) {
        $params = array(
                        'resource' => null,
                        'since'    => $since,
                        'limit'    => $limit,
                        'page'     => $page,
                        'store_id' => $this->getStoreId()
                       );

        $obj = Mage::getModel('bakerloo_restful/V1_' . $resource, $params);

        return $obj->get();
    }

    private function _getExportConfig($data) {

        if(!isset($data['total_count'])) {
            $data['total_count'] = 1;
            $data['total_pages'] = 1;
        }

        $_config = array(
            'perpage'      => $this->_pageSize,
            'totalrecords' => $data['total_count'],
            'totalpages'   => $data['total_pages'],
        );

        return serialize($_config);
    }

    public function trace($text, $force = false) {
        if(false === $force && false === $this->_debug) {
            return;
        }

        echo sprintf('%-30s', $text);
    }

    protected function _reset() {
        $this->_io = null;
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp() {
        return <<<USAGE
Usage:  php bakerloo.php -- [options]

  --storeid <int>        Specify Store ID
  --pagesize <int>       Set page size, default is 200
  --resource <string>    Generate specified entity, for example customers
  --trace <int>          Show information about activity on screen
  --startpage <int>      Start export on a given page > 1
  all                    Generate all entities: products, categories, customers, inventory
  help                   This help

USAGE;
    }


    public function loguear($data) {
        Mage::log($data, null, "CLI_LOG.log");
    }

}

$shell = new Ebizmarts_BakerlooShell_Bakerloo();
$shell->run();