<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_ThemeOne
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Themeone Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_ThemeOne
 * @author      Magestore Developer
 */
class Simi_Themeone_Adminhtml_Themeone_CategoriesController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Simi_ThemeOne_Adminhtml_ThemeoneController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('themeone/categories')
                ->_addBreadcrumb(
                        Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager')
        );
        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction() {
        $themeoneId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('themeone/categories')->load($themeoneId);

        if ($model->getId() || $themeoneId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('categories_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('themeone/categories');

            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager')
            );
            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('themeone/adminhtml_categories_edit'))
                    ->_addLeft($this->getLayout()->createBlock('themeone/adminhtml_categories_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('themeone')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    /**
     * save item action
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {


            $image_type = 'category';
            $image_type_id = $this->getRequest()->getParam('id');
            $storeId = Mage::app()->getStore()->getStoreId();
            if ($image_type_id == null)
                $image_type_id = 1;

            if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('filename');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);

                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //    (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS;
                    $result = $uploader->save($path, $_FILES['filename']['name']);
                    $data['filename'] = $result['file'];
                } catch (Exception $e) {
                    $data['filename'] = $_FILES['filename']['name'];
                }
            }
            $model = Mage::getModel('themeone/categories');
    if ($image_type_id != 4) {
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));
                $categorys = Mage::getSingleton('themeone/allcategory')->toOptionArray();
                foreach ($categorys as $category) {
                    if ($category["value"] == $data['category_id']) {
                        $model->setCategoryName($category["label"]);
                        break;
                    }
                }
            }
            else {
                $model->setId($this->getRequest()->getParam('id'));
            }
        


            try {
//                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
//                    $model->setCreatedTime(now())
//                        ->setUpdateTime(now());
//                } else {
//                    $model->setUpdateTime(now());
//                }
                 if ($image_type_id != 4) 
                $model->save();
                
                // Save Images
                if (!isset($data['radiophone'])) {
                    $data['radiophone'] = 1;
                }
              
                if (isset($data['images_phone_id'])) {
                    Mage::helper('themeone')->saveImageStore($data['images_phone_id'], $storeId, $image_type, $image_type_id, $_FILES, $data['radiophone'],"phone");
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('themeone')->__('Item was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('themeone')->__('Unable to find item to save')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
//    public function deleteAction()
//    {
//        if ($this->getRequest()->getParam('id') > 0) {
//            try {
//                $model = Mage::getModel('themeone/themeone');
//                $model->setId($this->getRequest()->getParam('id'))
//                    ->delete();
//                Mage::getSingleton('adminhtml/session')->addSuccess(
//                    Mage::helper('adminhtml')->__('Item was successfully deleted')
//                );
//                $this->_redirect('*/*/');
//            } catch (Exception $e) {
//                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
//                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
//            }
//        }
//        $this->_redirect('*/*/');
//    }

    /**
     * mass delete item(s) action
     */
//    public function massDeleteAction()
//    {
//        $themeoneIds = $this->getRequest()->getParam('themeone');
//        if (!is_array($themeoneIds)) {
//            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
//        } else {
//            try {
//                foreach ($themeoneIds as $themeoneId) {
//                    $themeone = Mage::getModel('themeone/themeone')->load($themeoneId);
//                    $themeone->delete();
//                }
//                Mage::getSingleton('adminhtml/session')->addSuccess(
//                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
//                    count($themeoneIds))
//                );
//            } catch (Exception $e) {
//                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
//            }
//        }
//        $this->_redirect('*/*/index');
//    }

    /**
     * mass change status for item(s) action
     */
//    public function massStatusAction()
//    {
//        $themeoneIds = $this->getRequest()->getParam('themeone');
//        if (!is_array($themeoneIds)) {
//            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
//        } else {
//            try {
//                foreach ($themeoneIds as $themeoneId) {
//                    Mage::getSingleton('themeone/themeone')
//                        ->load($themeoneId)
//                        ->setStatus($this->getRequest()->getParam('status'))
//                        ->setIsMassupdate(true)
//                        ->save();
//                }
//                $this->_getSession()->addSuccess(
//                    $this->__('Total of %d record(s) were successfully updated', count($themeoneIds))
//                );
//            } catch (Exception $e) {
//                $this->_getSession()->addError($e->getMessage());
//            }
//        }
//        $this->_redirect('*/*/index');
//    }

    /**
     * export grid item to CSV type
     */
//    public function exportCsvAction()
//    {
//        $fileName   = 'themeone.csv';
//        $content    = $this->getLayout()
//                           ->createBlock('themeone/adminhtml_themeone_grid')
//                           ->getCsv();
//        $this->_prepareDownloadResponse($fileName, $content);
//    }
//
//    /**
//     * export grid item to XML type
//     */
//    public function exportXmlAction()
//    {
//        $fileName   = 'themeone.xml';
//        $content    = $this->getLayout()
//                           ->createBlock('themeone/adminhtml_themeone_grid')
//                           ->getXml();
//        $this->_prepareDownloadResponse($fileName, $content);
//    }
//    
    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('themeone');
    }

}