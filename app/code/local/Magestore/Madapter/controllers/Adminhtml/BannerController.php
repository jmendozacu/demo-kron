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
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Madapter Adminhtml Controller
 * 
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @author  	Magestore Developer
 */
class Magestore_Madapter_Adminhtml_BannerController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Madapter_Adminhtml_MadapterController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('madapter/banner')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Banners Manager'), Mage::helper('adminhtml')->__('Banner Manager'));
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
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('madapter/banner')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data))
                $model->setData($data);

            Mage::register('banner_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('madapter/banner');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Banner'), Mage::helper('adminhtml')->__('Item banner'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('madapter/adminhtml_banner_edit'))
                    ->_addLeft($this->getLayout()->createBlock('madapter/adminhtml_banner_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('madapter')->__('Item does not exist'));
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
            if (isset($_FILES['banner_name']['name']) && $_FILES['banner_name']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('banner_name');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);

                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //	(file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    str_replace(" ","_",$_FILES['banner_name']['name']);
                    $path = Mage::getBaseDir('media') . DS .'madapter'. DS . 'banner';
                    $result = $uploader->save($path, $_FILES['banner_name']['name']);
                    $data['banner_name'] = $result['file'];
                } catch (Exception $e) {
                    $data['banner_name'] = $_FILES['banner_name']['name'];
                }
            }
            
            if (isset($data['banner_name']['delete']) && $data['banner_name']['delete'] == 1){
                $data['banner_name'] = '';
            }
            
            $model = Mage::getModel('madapter/banner');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {               

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('madapter')->__('Banner was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('madapter')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('madapter/banner');
                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Banner was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction() {
        $bannerIds = $this->getRequest()->getParam('madapter');
        if (!is_array($bannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannerIds as $bannerId) {
                    $madapter = Mage::getModel('madapter/banner')->load($bannerId);
                    $madapter->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($bannerIds)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass change status for item(s) action
     */
    public function massStatusAction() {
        $bannerIds = $this->getRequest()->getParam('madapter');
        if (!is_array($bannerIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($bannerIds as $bannerId) {
                    $bannerId = Mage::getSingleton('madapter/banner')
                            ->load($bannerId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($bannerIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'banner.csv';
        $content = $this->getLayout()->createBlock('madapter/adminhtml_banner_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'banner.xml';
        $content = $this->getLayout()->createBlock('madapter/adminhtml_banner_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('madapter');
    }

    

}