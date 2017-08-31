<?php

class Ebizmarts_BakerlooRestful_Model_Api_Backup extends Ebizmarts_BakerlooRestful_Model_Api_Api {


    /**
     * Process GET requests.
     *
     * @return type
     * @throws Exception
     */

    public function getModelName()
    {
        return __CLASS__;
    }

    public function getOrdersBackup(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response) {

        if(!$this->getStoreId()) {
            Mage::throwException('Please provide a Store ID.');
        }
        $store = $this->getStoreId();

        if(!file_exists(Mage::getBaseDir('var') . '/pos_orders_backup/' . $store)){
            Mage::throwException('No backup files for that store ID.');
        }else{
            $files_on_folder = scandir(Mage::getBaseDir('var') . '/pos_orders_backup/' . $store);

            if(count($files_on_folder) == 0){
                Mage::throwException('No backup files for that store ID.');
            }

            $file_name = $files_on_folder[count($files_on_folder)-1];
            $file_path = Mage::getBaseDir('var') . '/pos_orders_backup/' . $store . "/" . $file_name;

            Mage::helper('bakerloo_restful/pages')->returnBinary($file_path, $response, $file_name, 'application/zip');
            return;
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

        if(!array_key_exists('total_files',$_POST)) {
            Mage::throwException('Please provide total_files.');
        }
        $total_files = $_POST['total_files'];

        if(!array_key_exists('file_num',$_POST)) {
            Mage::throwException('Please provide file_num.');
        }
        $file_num = $_POST['file_num'];

        if(!array_key_exists('orders_backup_file',$_FILES)) {
            Mage::throwException('Please provide orders_backup_file.');
        }
        $file = $_FILES['orders_backup_file'];

        $date = date("Ymdhis");

        //check containing folder
        if(!file_exists(Mage::getBaseDir('var') . '/pos_orders_backup')){
            mkdir(Mage::getBaseDir('var') . '/pos_orders_backup');
        }
        if(!file_exists(Mage::getBaseDir('var') . '/pos_orders_backup/' . $store)){
            mkdir(Mage::getBaseDir('var') . '/pos_orders_backup/' . $store);
        }

        $save_file_path = Mage::getBaseDir('var') . '/pos_orders_backup/' . $store . '/' . $date . '_backup_part_' . $file_num . 'of' . $total_files;

        //save file
        $file_content = file_get_contents($file['tmp_name']);
        //encrypt
        //$file_content = Mage::helper('bakerloo_restful')->encryptOrderBackupFile($file_content);
        $save = file_put_contents($save_file_path,$file_content);

        if($save !== false){
            return $this;
        }else{
            Mage::throwException('Backup not saved.');
        }
    }

}