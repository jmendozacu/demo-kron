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
class Ebizmarts_PosShell_Pos extends Mage_Shell_Abstract {

    //@TODO: pcntl fork, via command line argument

    private $_resources = array(
        'categories',
        'products',
        'inventory',
        'customers',
    );

    private $_storeId   = null;
    private $_pageSize  = 200;
    private $_debug     = false;
    private $_startPage = 2;

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
            die("Please provide a store ID. See help for more information.\n");
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

            $data = Mage::helper('bakerloo_restful/pages')->getData($resource, -1, $this->_pageSize, $pageNumber, $this->getStoreId());

            Mage::helper('bakerloo_restful/pages')->storeData($resource, $data, $this->getStoreId(), $pageNumber);

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

            $data = Mage::helper('bakerloo_restful/pages')->getData($resource, -1, $this->_pageSize, $pageNumber, $this->getStoreId());

            $ioAdapter->write("page" . str_pad($pageNumber, 5, '0', STR_PAD_LEFT) . ".ser", serialize($data['page_data']));

            $this->trace("FINISH: Processing page {$pageNumber} " . Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s') . "\n\n");

        }

        $this->trace("********** Finished {$resource} " . Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s') . " <<< **********\n\n\n", true);

    }

    private function getIo($storeId, $resource, $reset) {
        return Mage::helper('bakerloo_restful/pages')->getIo($storeId, $resource, $reset);
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
Usage:  php pos.php -- [options]

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

$shell = new Ebizmarts_PosShell_Pos();
$shell->run();