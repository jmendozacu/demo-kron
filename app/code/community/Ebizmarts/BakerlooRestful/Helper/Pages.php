<?php
/**
 * Static pages HELPER.
 */

class Ebizmarts_BakerlooRestful_Helper_Pages {

    const MATCH_PAGE_NAME = "/(page)(\\d{5})(\\.)(ser)/is";

    private $_io = null;

    public function getExportConfig($data) {

        if(!isset($data['total_count'])) {
            $data['total_count'] = 1;
            $data['total_pages'] = 1;
        }

        $_config = array(
            'perpage'      => $data['page_count'],
            'totalrecords' => $data['total_count'],
            'totalpages'   => $data['total_pages'],
        );

        return serialize($_config);
    }

    public function getData($resource, $since, $limit, $page = 1, $storeId = 0) {
        $params = array(
            'resource' => null,
            'since'    => $since,
            'limit'    => $limit,
            'page'     => $page,
            'store_id' => $storeId
        );

        $obj = Mage::getModel('bakerloo_restful/api_' . $resource, $params);

        return $obj->get();
    }

    public function storeData($resource, array $data, $storeId, $pageNumber) {

        //Path to file: For example, /path/to/magento/var/pos/1/products
        $reset = false;

        if(1 === $pageNumber) {
            $reset = true;
        }

        $ioAdapter = $this->getIo($storeId, $resource, $reset);

        $_data = !isset($data['page_data']) ? $data : $data['page_data'];

        //Save export global information
        if(1 === $pageNumber) {
            $ioAdapter->write("_pagedata.ser", $this->getExportConfig($data));
        }

        $ioAdapter->write("page" . str_pad($pageNumber, 5, '0', STR_PAD_LEFT) . ".ser", serialize($_data));

        unset($data);
    }

    public function getIo($storeId, $resource, $reset) {

        if(is_null($this->_io)) {
            $path = Mage::helper('bakerloo_restful/cli')->getPathToDb($storeId, $resource, $reset);
            $ioAdapter = new Varien_Io_File();
            $ioAdapter->open(array('path' => $path));
            $this->_io = $ioAdapter;
        }

        return $this->_io;
    }

    public function getZippedPages(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, $resource = null) {

        $reset = false;
        $storeId = (int)$request->getHeader(Mage::helper('bakerloo_restful')->getStoreIdHeader());

        if(is_null($resource)) {
            $params = $request->getParams();
            if(is_array($params) and !empty($params)) {
                if(count($params)) {
                    $keys = array_keys($params);
                    $resource = $keys[0];
                }
            }
        }

        $path = Mage::helper('bakerloo_restful/cli')->getPathToDb($storeId, $resource, $reset);

        try {

            $zipiName = 'pages.zip';
            $zipName = $path . DS . $zipiName;

            if(file_exists($zipName)) {
                $this->returnBinary($zipName, $response, $zipiName, 'application/zip');
                return;
            }

            $pages = array();

            $io = $this->getIo($storeId, $resource, $reset);
            $pageData = unserialize($io->read('_pagedata.ser'));

            if(false === $pageData) {
                Mage::throwException('No pages found to ZIP. Resource: ' . $resource);
            }

            $iterator = new DirectoryIterator($path);
            foreach ($iterator as $fileInfo) {

                $fileName = $fileInfo->getFilename();

                if(preg_match(self::MATCH_PAGE_NAME, $fileName) !== 1) {
                    continue;
                }

                array_push($pages, $fileName);
            }

            usort($pages, array($this, "sort_tree"));

            $zip = new ZipArchive;
            $zip->open($zipName, ZipArchive::OVERWRITE);

            foreach($pages as $_page) {
                $fileData = unserialize($io->read($_page));

                $toStore = $pageData;
                $toStore['page_data'] = $fileData;

                $okZip = $zip->addFromString(str_replace('.ser', '.json', $_page), json_encode($toStore));
                if(false === $okZip) {
                    Mage::throwException("Could not compress: " . $fileName);
                }
            }

            $zip->close();

            $this->returnBinary($zipName, $response, $zipiName, 'application/zip');

        }catch(Exception $ex) {
            //Directory is empty or something.
            $response->setHttpResponseCode(500)
                ->setBody($ex->getMessage());
        }
    }

    public function returnBinary($filename, Zend_Controller_Response_Abstract $response, $name, $type) {

        $response
        ->setHttpResponseCode(200)
        ->setHeader('Content-Type', $type, true)
        ->setHeader('Content-Disposition', 'attachment; filename=' . $name, true)
        ->setHeader('Content-Length', filesize($filename))
        ->setHeader('Pragma', 'no-cache', true)
        ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->clearBody();
        $response->sendHeaders();

        readfile($filename);

    }

    public function getDB(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, $compressed = true, $resource = null) {

        $reset = false;
        $storeId = (int)$request->getHeader(Mage::helper('bakerloo_restful')->getStoreIdHeader());

        if(is_null($resource)) {
            $params = $request->getParams();
            if(is_array($params) and !empty($params)) {
                if(count($params)) {
                    $keys = array_keys($params);
                    $resource = $keys[0];
                }
            }
        }

        $validResources = array(
          'products' => array(
              'tableName' => 'Product',
              'create' => array('CREATE TABLE [Product] (
                           [product_id] INTEGER NOT NULL PRIMARY KEY,
                           [sku] TEXT NULL,
                           [name] TEXT  NULL,
                           [price] REAL  NULL,
                           [special_price] REAL  NULL,
                           [last_update] TIMESTAMP  NULL,
                           [type] TEXT  NULL,
                           [visibility] INTEGER NULL,
                           [barcode] TEXT NULL,
                           [json] TEXT  NULL,
                           [status] INTEGER NULL DEFAULT 1);',
                  'CREATE TABLE [ProductCategory] (
                      [product_id] INTEGER NULL,
                      [category_id] INTEGER NULL,
                      [position] INTEGER default 1,
                      PRIMARY KEY ([product_id],[category_id])
                      );',
                'CREATE TABLE [ProductBarcodes] (
                [barcode] TEXT,
                [product_id] not null,
                primary key(barcode,product_id)
                );'
                  ),
              ),
          'inventory' => array(
              'tableName' => 'ProductInventory',
              'create' => 'CREATE TABLE [ProductInventory] (
                           [product_id] INTEGER NOT NULL PRIMARY KEY,
                           [json] TEXT NULL,
                           [updated_at] TIMESTAMP NULL);',
              ),
          'customers' => array(
              'tableName' => 'Customer',
              'create' => 'CREATE TABLE [Customer] (
                           [customer_id] INTEGER NOT NULL PRIMARY KEY,
                           [firstname] TEXT NULL,
                           [lastname] TEXT NULL,
                           [email] TEXT NULL,
                           [group_id] INTEGER NULL,
                           [json] TEXT NULL,
                           [updated_at] TIMESTAMP NULL);',
              ),
        );

        if(!array_key_exists($resource, $validResources)) {
            Mage::throwException("Invalid resource provided.");
        }

        $path = Mage::helper('bakerloo_restful/cli')->getPathToDb($storeId, $resource, $reset);

        try {

            $sqliteName = $validResources[$resource]['tableName'] . '.sqlite';
            $zipiName   = $validResources[$resource]['tableName'] . '.zip';

            $basePath = $path . DS;
            $dbName   = $basePath . $sqliteName;
            $zipName  = $basePath . $zipiName;

            if(file_exists($dbName) and !$compressed) {
                $this->returnBinary($dbName, $response, $sqliteName, 'application/x-sqlite3');
                return;
            }
            else {
                if(file_exists($zipName)) {
                    $this->returnBinary($zipName, $response, $zipiName, 'application/zip');
                    return;
                }
            }

            $db = new SQLite3($dbName);

            $createTableSQL = $validResources[$resource]['create'];
            if(is_array($createTableSQL)) {
                for($i = 0; $i < count($createTableSQL); $i++) {
                    $db->exec($createTableSQL[$i]);
                }
            }
            else {
                $db->exec($createTableSQL);
            }

            $db->exec("BEGIN EXCLUSIVE TRANSACTION;");

            $stmt         = '';
            $recordsCount = 0;

            $io = $this->getIo($storeId, $resource, $reset);
            $iterator = new DirectoryIterator($path);
            foreach ($iterator as $fileInfo) {

                $fileName = $fileInfo->getFilename();

                if(preg_match(self::MATCH_PAGE_NAME, $fileName) !== 1) {
                    continue;
                }

                $fileData = unserialize($io->read($fileName));

                if($fileData === false) {
                    $pageError = "Could not decode page: " . $fileName;

                    Mage::log($pageError, null, 'POS-SQLite.log', true);

                    $db->close();
                    if(file_exists($dbName)) {
                        @unlink($dbName);
                    }

                    Mage::throwException($pageError);
                }

                if(is_array($fileData) and !empty($fileData)) {
                    foreach($fileData as $_item) {

                        $json = SQLite3::escapeString(json_encode($_item));

                        if('products' == $resource) {
                            $name = trim(preg_replace('/\s+/', ' ', $_item["name"]));
                            $name = SQLite3::escapeString("{$name}");
                            $sku  = SQLite3::escapeString("{$_item["sku"]}");

                            $stmt .= 'INSERT INTO Product(product_id, sku, name, price, special_price, last_update, type, visibility, json, barcode, status) VALUES ('.$_item['product_id'].', \''.$sku.'\', \''. $name . '\','.$_item['price'].','.$_item['special_price'].',\''.$_item['last_update'].'\',"'.$_item['type'].'",'.$_item['visibility'].',\''.$json.'\',\''.$_item['barcode'].'\','.$_item['status'].');';

                            if(isset($_item['categories']) and is_array($_item['categories']) and !empty($_item['categories'])) {
                                for($i = 0; $i < count($_item['categories']); $i++) {
                                    $stmt .= 'INSERT INTO ProductCategory(product_id, category_id, position) VALUES ('.$_item['product_id'].','.$_item['categories'][$i]['category_id'].', '.$_item['categories'][$i]['position'].');';
                                }
                            }

                            $barcode = Mage::helper('bakerloo_restful')->getProductBarcode($_item['product_id'], $storeId);

                            $_barcode = explode(',', $barcode);
                            for($i = 0; $i < count($_barcode); $i++) {
                                $barcodeTemp = SQLite3::escapeString("{$_barcode[$i]}");
                                $stmt .= 'INSERT INTO ProductBarcodes(barcode, product_id) VALUES (\''. $barcodeTemp . '\', ' . $_item['product_id'] . ');';
                            }

                        }
                        else {
                            if('inventory' == $resource) {
                                $stmt .= 'INSERT INTO ProductInventory(product_id, json, updated_at) VALUES ('.$_item['product_id'].', \''.$json.'\', \''.$_item['updated_at'].'\');';
                            }
                            else {
                                if('customers' == $resource) {
                                    $stmt .= 'INSERT INTO Customer(customer_id, firstname, lastname, email, group_id, json, updated_at) VALUES ('.$_item['customer_id'].', \''.$_item['firstname'].'\', \''.$_item['lastname'].'\', \''.$_item['email'].'\', '.$_item['group_id'].', \''.$json.'\', \''.$_item['updated_at'].'\');';
                                }
                            }
                        }

                        $recordsCount++;
                    }
                }

                unset($fileData);
                unset($_item);

            }

            $db->exec($stmt);

            $db->exec("END TRANSACTION;");

            $db->close();

            if($recordsCount == 0) {
                if(file_exists($dbName)) {
                    @unlink($dbName);
                }

                Mage::throwException('No pages found to create DB. Resource: ' . $resource);
            }


            $dbcheck = new SQLite3($dbName);
            $count = $dbcheck->querySingle('SELECT COUNT(*) FROM ' . $validResources[$resource]['tableName'] . ';');

            if(((int)$count) !== $recordsCount) {
                if(file_exists($dbName)) {
                    @unlink($dbName);
                }

                Mage::log($stmt, null, 'POS-SQLite.log', true);
                Mage::log($count, null, 'POS-SQLite.log', true);
                Mage::log($recordsCount, null, 'POS-SQLite.log', true);

                Mage::throwException("Corrupted database, please try again.");
            }


            $zip = new ZipArchive;
            $zip->open($zipName, ZipArchive::OVERWRITE);
            $okZip = $zip->addFile($dbName, $sqliteName);
            if(false === $okZip) {
                Mage::throwException("Could not compress: " . $zipiName);
            }
            $zip->close();

            if($compressed) {
                $this->returnBinary($zipName, $response, $zipiName, 'application/zip');
            }
            else {
                $this->returnBinary($dbName, $response, $sqliteName, 'application/x-sqlite3');
            }

        } catch(Exception $ex) {
            //Directory is empty or something.
            $response->setHttpResponseCode(500)
                ->setBody($ex->getMessage());
        }

    }

    public function sort_tree($a, $b) {
        return strcasecmp($a, $b);
    }

    public function getOrdersBackup(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, $resource = null) {

        $reset = false;
        $storeId = (int)$request->getHeader(Mage::helper('bakerloo_restful')->getStoreIdHeader());

        if(is_null($resource)) {
            $params = $request->getParams();
            if(is_array($params) and !empty($params)) {
                if(count($params)) {
                    $keys = array_keys($params);
                    $resource = $keys[0];
                }
            }
        }

        $path = Mage::helper('bakerloo_restful/cli')->getPathToDb($storeId, $resource, $reset);

        try {

            $zipiName = 'pages.zip';
            $zipName = $path . DS . $zipiName;

            if(file_exists($zipName)) {
                $this->returnBinary($zipName, $response, $zipiName, 'application/zip');
                return;
            }

            $pages = array();

            $io = $this->getIo($storeId, $resource, $reset);
            $pageData = unserialize($io->read('_pagedata.ser'));

            if(false === $pageData) {
                Mage::throwException('No pages found to ZIP. Resource: ' . $resource);
            }

            $iterator = new DirectoryIterator($path);
            foreach ($iterator as $fileInfo) {

                $fileName = $fileInfo->getFilename();

                if(preg_match(self::MATCH_PAGE_NAME, $fileName) !== 1) {
                    continue;
                }

                array_push($pages, $fileName);
            }

            usort($pages, array($this, "sort_tree"));

            $zip = new ZipArchive;
            $zip->open($zipName, ZipArchive::OVERWRITE);

            foreach($pages as $_page) {
                $fileData = unserialize($io->read($_page));

                $toStore = $pageData;
                $toStore['page_data'] = $fileData;

                $okZip = $zip->addFromString(str_replace('.ser', '.json', $_page), json_encode($toStore));
                if(false === $okZip) {
                    Mage::throwException("Could not compress: " . $fileName);
                }
            }

            $zip->close();

            $this->returnBinary($zipName, $response, $zipiName, 'application/zip');

        }catch(Exception $ex) {
            //Directory is empty or something.
            $response->setHttpResponseCode(500)
                ->setBody($ex->getMessage());
        }
    }

}