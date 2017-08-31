<?php

class Wyomind_Simplegoogleshopping_Adminhtml_SimplegoogleshoppingController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {

        $this->loadLayout()
                ->_setActiveMenu('catalog/googleshopping')
                ->_addBreadcrumb($this->__('Google Shopping'), ('Google Shopping'));

        return $this;
    }

    public function indexAction() {




        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {




        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('simplegoogleshopping/simplegoogleshopping')->load($id);


        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('simplegoogleshopping_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('catalog/googleshopping')->_addBreadcrumb($this->__('Google Shopping'), ('Google Shopping'));
            $this->_addBreadcrumb($this->__('Google Shopping'), ('Google Shopping'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            //$this->_addContent($this->getLayout()->createBlock('simplegoogleshopping/adminhtml_simplegoogleshopping_edit'));
            $this->_addContent($this->getLayout()
                            ->createBlock('simplegoogleshopping/adminhtml_simplegoogleshopping_edit'))
                    ->_addLeft($this->getLayout()
                            ->createBlock('simplegoogleshopping/adminhtml_simplegoogleshopping_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simplegoogleshopping')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {


        // check if data sent
        if ($data = $this->getRequest()->getPost()) {

            // init model and set data
            $model = Mage::getModel('simplegoogleshopping/simplegoogleshopping');

            if ($this->getRequest()->getParam('simplegoogleshopping_id')) {
                $model->load($this->getRequest()->getParam('simplegoogleshopping_id'));
            }


            $model->setData($data);

            // try to save it
            try {

                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('simplegoogleshopping')->__('The data feed has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);



                if ($this->getRequest()->getParam('continue')) {
                    $this->getRequest()->setParam('id', $model->getId());
                    $this->_forward('edit');
                    return;
                }


                // go to grid or forward to generate action
                if ($this->getRequest()->getParam('generate')) {
                    $this->getRequest()->setParam('simplegoogleshopping_id', $model->getId());
                    $this->_forward('generate');
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('simplegoogleshopping_id' => $this->getRequest()->getParam('simplegoogleshopping_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction() {

        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('simplegoogleshopping/simplegoogleshopping');
                $model->setId($id);
                // init and load googleshopping model

                /* @var $googleshopping Mage_Simplegoogleshopping_Model_Simplegoogleshopping */
                $model->load($id);
                // delete file
                if ($model->getSimplegoogleshoppingFilename() && file_exists($model->getPreparedFilename())) {
                    unlink($model->getPreparedFilename());
                }
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('simplegoogleshopping')->__('The data feed has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('simplegoogleshopping_id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('simplegoogleshopping')->__('Unable to find a data feed to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    public function sampleAction() {


        // init and load googleshopping model
        $id = $this->getRequest()->getParam('simplegoogleshopping_id');


        $googleshopping = Mage::getModel('simplegoogleshopping/simplegoogleshopping');
        $googleshopping->setId($id);
        $googleshopping->_limit = Mage::getStoreConfig("simplegoogleshopping/system/preview");

        $googleshopping->_display = true;

        // if googleshopping record exists
        $googleshopping->load($id);

        try {
            $content = $googleshopping->generateXml();
            if ($googleshopping->_demo) {
                $this->_getSession()->addError(Mage::helper('simplegoogleshopping')->__("Invalid license."));
                Mage::getConfig()->saveConfig('simplegoogleshopping/license/activation_code', '', 'default', '0');
                Mage::getConfig()->cleanCache();
                $this->_redirect('*/*/');
            }
            else
                print($content);
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_getSession()->addException($e, Mage::helper('simplegoogleshopping')->__('Unable to generate the data feed.'));
            $this->_redirect('*/*/');
        }
    }

    public function generateAction() {

        // init and load googleshopping model
        $id = $this->getRequest()->getParam('simplegoogleshopping_id');

        $googleshopping = Mage::getModel('simplegoogleshopping/simplegoogleshopping');
        $googleshopping->setId($id);
        $limit = $this->getRequest()->getParam('limit');
        $googleshopping->_limit = $limit;


        // if googleshopping record exists
        if ($googleshopping->load($id)) {


            try {

                $time_start = time(true);
                $googleshopping->generateXml();
                $time_end = time(true);

                $time = $time_end - $time_start;
                if ($time < 60)
                    $time = ceil($time) . ' sec. ';
                else
                    $time = floor($time / 60) . ' min. ' . ($time % 60) . ' sec.';

                $unit = array('b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb');
                $memory = @round(memory_get_usage() / pow(1024, ($i = floor(log(memory_get_usage(), 1024)))), 2) . ' ' . $unit[$i];


                $fileName = preg_replace('/^\//', '', $googleshopping->getSimplegoogleshoppingPath() . $googleshopping->getSimplegoogleshoppingFilename());
                $url = (Mage::app()->getStore($googleshopping->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $fileName);
                $report = "
                    
                    <table>
                   
                    <tr><td align='right' width='150'>Processing time &#8614; </td><td>$time</td></tr>
                    <tr><td align='right'>Memory usage &#8614; </td><td>$memory</td></tr>
                    <tr><td align='right'>Product inserted &#8614; </td><td>$googleshopping->_inc</td></tr>
                    <tr><td align='right'>Generated file &#8614; </td><td><a href='$url?r=" . time() . "' target='_blank'>$url</a></td></tr>
                    </table>";
                if ($googleshopping->_demo) {
                    $this->_getSession()->addError(Mage::helper('simplegoogleshopping')->__("Invalid license."));
                    Mage::getConfig()->saveConfig('simplegoogleshopping/license/activation_code', '', 'default', '0');
                    Mage::getConfig()->cleanCache();
                } else {
                    $this->_getSession()->addSuccess(Mage::helper('simplegoogleshopping')->__('The data feed "%s" has been generated.', $googleshopping->getSimplegoogleshoppingFilename()));
                    $this->_getSession()->addSuccess($report);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->addException($e, Mage::helper('simplegoogleshopping')->__('Unable to generate the data feed.'));
            }
        } else {
            $this->_getSession()->addError(Mage::helper('simplegoogleshopping')->__('Unable to find a data feed to generate.'));
        }

        if ($this->getRequest()->getParam('generate'))
            $this->_redirect('*/*/edit', array("id" => $id));
        else
            $this->_redirect('*/*');
    }

    public function categoriesAction() {
        $i = 0;
        $io = new Varien_Io_File();
        $realPath = $io->getCleanPath(Mage::getBaseDir() . "/lib/Google/taxonomy.txt");
        $io->streamOpen($realPath, "r+");
        while (false !== ($line = $io->streamRead())) {

            if (stripos($line, $this->getRequest()->getParam('s')) !== FALSE)
                echo $line;
        }
        die();
    }

    function libraryAction() {

        $resource = Mage::getSingleton('core/resource');
        $read = $resource->getConnection('core_read');
        $tableEet = $resource->getTableName('eav_entity_type');
        $select = $read->select()->from($tableEet)->where('entity_type_code=\'catalog_product\'');
        $data = $read->fetchAll($select);
        $typeId = $data[0]['entity_type_id'];

        function cmp($a, $b) {

            return ($a['attribute_code'] < $b['attribute_code']) ? -1 : 1;
        }

        /*  Liste des  attributs disponible dans la bdd */

        $attributesList = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($typeId)
                ->addSetInfo()
                ->getData();
        $selectOutput = null;
        $attributesList[] = array("attribute_code" => "qty", "frontend_label" => "Quantity");
        $attributesList[] = array("attribute_code" => "is_in_stock", "frontend_label" => "Is in stock");
        $attributesList[] = array("attribute_code" => "entity_id", "frontend_label" => "Product ID");
        usort($attributesList, "cmp");

        $tabOutput = '<div id="dfm-library"><ul><h3>References</h3> ';
        $contentOutput = '<table >';





        $tabOutput .=" <li><a href='#attributes'>Store attributes</a></li>";


        $contentOutput .="<tr><td><a name='attributes'></a><b>Store attributes</b></td></tr>";
        foreach ($attributesList as $attribute) {


            if (!empty($attribute['attribute_code']))
                $contentOutput.= "<tr><td>" . $attribute['frontend_label'] . "</td><td><span class='pink'>{" . $attribute['attribute_code'] . "}</span></td></tr>";
        }






        $tabOutput .=" <li><a target='_blank' href='http://wyomind.com/google-shopping-magento.html?src=sgs-library&directlink=documentation#Special_attributes'>Special Attributes</a></li>";
        $tabOutput .=" <li><a target='_blank' href='http://wyomind.com/google-shopping-magento.html?src=sgs-library&directlink=documentation#Basic_attributes_&_basic_options'>Attribute options</a></li>";
        $tabOutput .=" <li><a target='_blank' href='http://wyomind.com/google-shopping-magento.html?src=sgs-library&directlink=documentation#Simple_Google_Shopping_tutorial'>Tutorial</a></li>";
         $tabOutput .=" <li><a target='_blank' href='http://wyomind.com/google-shopping-magento.html?src=sgs-library&directlink=documentation#top'>More documentation</a></li>";


        /*

          $myCustomOptions = new MyCustomoptions;
          foreach ($myCustomOptions->_getAll() as $group => $Options) {
          $tabOutput .=" <li><a href='#" . $group . "'> " . $group . "</a></li>";
          $contentOutput .="<tr><td><a name='" . $group . "'></a><b>" . $group . "</b></td></tr>";
          foreach ($Options as $opt) {
          $contentOutput.= "<tr><td><span class='pink'>{attribute_code,<span class='green'>[" . $opt . "]</span>}</span></td></tr>";
          }
          }
         */
        $contentOutput .="</table></div>";
        $tabOutput .= '</ul>';
        die($tabOutput . $contentOutput);
    }

}

