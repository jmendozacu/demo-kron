<?php

/**
 * @package    Magedevgroup_ScrappageScheme
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_ScrappageScheme_Adminhtml_ScrappageController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Show grid in Admin with products(scrappagescheme)
     */
    public function indexAction()
    {
        $scrappageschemeBlock = $this->getLayout()
            ->createBlock('scrappagescheme_adminhtml/scrappageScheme');

        $this->loadLayout()
            ->_addContent($scrappageschemeBlock)
            ->renderLayout();
    }

    /**
     * Load table of Empty products(without discount percentage) from database to file in ".csv" format
     */
    public function exportCsvEmptyAction()
    {
        $path = Mage::getBaseDir('export');
        $fileName = $path . DS . 'Kronosav' . '.csv';

        $file = fopen($fileName, 'w');

        /** @var Mage_Core_Model_Mysql4_Collection_Abstract $products */
        $products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('name');

        //make header
        $data = array(
            'id',
            'name',
            'sku',
            'discount_percentage',
        );
        fputcsv($file, $data);

        $i = 1;
        /** @var Mage_Catalog_Model_Product $product */
        foreach ($products as $product) {
            $data = array(
                $i++,
                $product->getName(),
                $product->getSku(),
                0, //discount percentage
            );

            fputcsv($file, $data);
        }

        fclose($file);

        $this->_prepareDownloadResponse('scrappagescheme.csv',
            array(
                'type' => 'filename',
                'value' => $fileName,
                'rm' => true,
            )
        );
    }

    /**
     * Load table of Current products(with discount percentage) from database to file in ".csv" format
     */
    public function exportCsvCurrentAction()
    {
        $path = Mage::getBaseDir('export');
        $fileName = $path . DS . 'Kronosav' . '.csv';

        $file = fopen($fileName, 'w');

        /** @var Mage_Core_Model_Mysql4_Collection_Abstract $products */
        $products = Mage::getModel('scrappagescheme/scrap')->getCollection();

        //make header
        $data = array(
            'id',
            'name',
            'sku',
            'discount_percentage',
        );
        fputcsv($file, $data);

        $i = 1;
        /** @var Mage_Catalog_Model_Product $product */
        foreach ($products as $product) {
            $data = array(
                $i++,
                $product->getName(),
                $product->getSku(),
                $product->getData('percentage'), //discount percentage
            );

            fputcsv($file, $data);
        }

        fclose($file);

        $this->_prepareDownloadResponse('scrappagescheme.csv',
            array(
                'type' => 'filename',
                'value' => $fileName,
                'rm' => true,
            )
        );
    }

    /**
     * Upload csv file on server & table in DataBase
     */
    public function uploadAction()
    {

        if ($data = $this->getRequest()->getPost()) {

            if (isset($_FILES['file_path']['name']) && $_FILES['file_path']['name'] != '') {
                try {
                    $uploader = new Varien_File_Uploader('file_path');
                    // Only CSV extention would work
                    $uploader->setAllowedExtensions(array('csv'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);

                    $path = Mage::getBaseDir('var') . DS . 'import' . DS;

                    $result = $uploader->save($path, $_FILES['file_path']['name']);

                    //check data in download .csv file
                    $this->checkData($path . $result['file']);

                    $this->deleteOptions();
                    $this->importTable($path . $result['file']);
                    $this->updateOptions();
                } catch
                (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Checking Scrappage Scheme data in CSV file
     */
    private function checkData($file)
    {
        $csv = new Varien_File_Csv();
        $data = $csv->getData($file);

        for ($i = 0; $i < count($data); $i++) {
            //miss header
            if ($i == 0) {
                continue;
            }

            $str = $data[$i][3]; // discount percentage
            $str = preg_replace("/[^0-9]/", '', $str);
            if ((int)$str > 100) {
                throw new Exception("Incorrect percent in -> " . $data[$i][1] . " [" . $data[$i][2] . "]");
            }
        }
    }

    /**
     * Delete 'Scrappage Scheme Trade In' options in Products
     */
    private function deleteOptions()
    {
        Mage::getResourceModel('scrappagescheme/scrap')->truncate();

        /** @var Mage_Catalog_Model_Product $product */
        $products = Mage::getModel('catalog/product')->getCollection();

        foreach ($products as $_product) {
            $product = Mage::getModel('catalog/product')->load($_product->getId());
            $customOptions = $product->getOptions();

            foreach ($customOptions as $key => $option) {
                if ($option->getTitle() == 'Scrappage Scheme Trade In') {
                    $option->delete();
                    $product->save();
                }
            }
        }
    }

    /**
     * Load file of Products(scrap scheme) to DataBase
     */
    private function importTable($file)
    {
        $csv = new Varien_File_Csv();
        $data = $csv->getData($file);

        $scrappageModel = Mage::getModel('scrappagescheme/scrap');

        for ($i = 0; $i < count($data); $i++) {
            if ($i == 0) {
                continue;
            }

            $scrappageData = array(
                "id" => $data[$i][0],
                "name" => $data[$i][1],
                "sku" => $data[$i][2],
                "percentage" => (int)$data[$i][3],
                "scrap_status" => 0,
            );

            $scrappageModel->setData($scrappageData)->save();
        }
    }

    /**
     * Set 'Scrappage Scheme Trade In' options in Products
     */
    private function updateOptions()
    {
        $scrappageCollection = Mage::getModel('scrappagescheme/scrap')->getCollection()
            ->addFieldToFilter('scrap_status', 0)
            ->addFieldToFilter('percentage', array('gt' => 0))
            ->setOrder('scrap_id', 'asc');

        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $successCount = $failedCount = $errorCount = 1;

        if (count($scrappageCollection)) {
            foreach ($scrappageCollection as $data) {
                $product = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('price', array('gt' => 0))
                    ->addAttributeToFilter('sku', array('eq' => $data['sku']))->getFirstItem();

                if ($product->getName()) {

                    $optionInstance = $product->getOptionInstance()->unsetOptions();
                    $product->setHasOptions(1);

                    $option = $this->getProductOption($data['percentage']);

                    if (isset($option['is_require']) && ($option['is_require'] == 1)) {
                        $product->setRequiredOptions(1);
                    }
                    $product->setPay4leterEnable(360);
                    $product->setPay4leterPlans(407);

                    $optionInstance->addOption($option);
                    $optionInstance->setProduct($product);
                    try {

                        $product->save();

                        Mage::getModel('scrappagescheme/scrap')->load($data['scrap_id'])->setStatus(1)->save();

                        // Success
                        Mage::log('Name :' . $product->getName(), null, 'MarchScrappageSuccessProducts.log', true);
                        Mage::log('Sku :' . $product->getSku(), null, 'MarchScrappageSuccessProducts.log', true);
                        Mage::log('Price :' . $product->getPrice(), null, 'MarchScrappageSuccessProducts.log', true);
                        Mage::log('Count :' . $successCount++, null, 'MarchScrappageSuccessProducts.log', true);
                        Mage::log('-----------------------------', null, 'MarchScrappageSuccessProducts.log', true);

                    } catch (Exception $e) {

                        // Error
                        Mage::log($product->getId(), null, 'MarchScrappageErrorProducts.log', true);
                        Mage::log($data['sku'] . ' ::' . $data['sku'], null, 'MarchScrappageErrorProducts.log', true);
                        Mage::log($errorCount++, null, 'MarchScrappageSuccessProducts.log', true);
                        Mage::log('-----------------------------', null, 'MarchScrappageErrorProducts.log', true);

                    }

                } else {
                    // Failed
                    Mage::log('Name :' . $data['name'], null, 'MarchScrappageFailedProducts.log', true);
                    Mage::log('Sku :' . $data['sku'], null, 'MarchScrappageFailedProducts.log', true);
                    Mage::log('Count :' . $failedCount++, null, 'MarchScrappageFailedProducts.log', true);
                    Mage::log('-----------------------------', null, 'MarchScrappageFailedProducts.log', true);
                }

                Mage::log('Name :' . $data['name'], null, 'MarchScrappageRunProductList.log', true);
                Mage::log('Sku :' . $data['sku'], null, 'MarchScrappageRunProductList.log', true);
                Mage::log('-----------------------------', null, 'MarchScrappageRunProductList.log', true);
            }
        } else {
            Mage::log('=============END===============', null, 'MarchScrappageCompleted.log', true);
        }
    }

    /**
     * Create Product Option add Discount Percent
     *
     * @param $discountPercent Discount Percent
     * @return array Product Option
     */
    private function getProductOption($discountPercent)
    {
        $option = array(
            'title' => 'Scrappage Scheme Trade In',
            'type' => 'radio',
            'is_require' => 1,
            'sort_order' => 0,
            'values' => array(
                array(
                    'title' => 'No thanks',
                    'price' => 0,
                    'price_type' => 'percent',
                    'sku' => '',
                    'sort_order' => 1
                ),
                array(
                    'title' => 'Yes I would like to trade in my old item',
                    'price' => -$discountPercent,
                    'price_type' => 'percent',
                    'sku' => '',
                    'sort_order' => 1
                )
            )
        );

        return $option;
    }
}
