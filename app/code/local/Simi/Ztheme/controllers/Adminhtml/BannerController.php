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
 * @package     Magestore_Ztheme
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Ztheme Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Ztheme
 * @author      Magestore Developer
 */
class Simi_Ztheme_Adminhtml_BannerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Simi_Ztheme_Adminhtml_ZthemeController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('ztheme/banner')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Banners Manager'),
                Mage::helper('adminhtml')->__('Banner Manager')
            );
        return $this;
    }
 
    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $zthemeId     = $this->getRequest()->getParam('banner_id');
        $model  = Mage::getModel('ztheme/banner')->load($zthemeId);
        if ($model->getId() || $zthemeId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            
            Mage::register('ztheme_banner_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('ztheme/banner');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Banner Manager'),
                Mage::helper('adminhtml')->__('Banner Manager')
            );
            /*
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Banner News'),
                Mage::helper('adminhtml')->__('Banner News')
            );
            */
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('ztheme/adminhtml_banner_edit'))
                ->_addLeft($this->getLayout()->createBlock('ztheme/adminhtml_banner_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('ztheme')->__('Banner does not exist')
            );
            $this->_redirect('*/*/');
        }
    }
 
    public function newAction()
    {
        $this->_forward('edit');
    }
 
    /**
     * save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if (isset($_FILES['banner_name']['name']) && $_FILES['banner_name']['name'] != '') {
                try {
                    /* Starting upload */    
                    $uploader = new Varien_File_Uploader('banner_name');
                    
                    // Any extention would work
                       $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    
                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //    (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);
                    
                    // We set media as the upload dir
                    str_replace(" ", "_", $_FILES['banner_name']['name']);                    
                    $website = $data['website_id'];
                    $website= $data['website_id'];
                   if($website==null) $website=0;
                    $path = Mage::getBaseDir('media') . DS . 'simi' . DS . 'ztheme' . DS . 'banner' . DS . $website;
                    if (!is_dir($path)) {
                        try {
                            mkdir($path, 0777, TRUE);
                        } catch (Exception $e) {
                            
                        }
                    }
                    
                    $result = $uploader->save($path, $_FILES['banner_name']['name'] );
                    $data['banner_name'] = $result['file'];
                } catch (Exception $e) {
                    $data['banner_name'] = $_FILES['banner_name']['name'];
                }
            }
            else {
                $bannerFile = $data['banner_name'];
                if ($bannerFile)
                    $data['banner_name'] = $bannerFile[0];
            }
            
            if (isset($_FILES['banner_name_tablet']['name']) && $_FILES['banner_name_tablet']['name'] != '') {
                try {
                    /* Starting upload */    
                    $uploader = new Varien_File_Uploader('banner_name_tablet');
                    
                    // Any extention would work
                       $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    
                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //    (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);
                    
                    // We set media as the upload dir
                    str_replace(" ", "_", $_FILES['banner_name_tablet']['name']);                    
                    $website = $data['website_id'];
                    $website= $data['website_id'];
                   if($website==null) $website=0;
                    $path = Mage::getBaseDir('media') . DS . 'simi' . DS . 'ztheme' . DS . 'banner_tab' . DS . $website;
                    if (!is_dir($path)) {
                        try {
                            mkdir($path, 0777, TRUE);
                        } catch (Exception $e) {
                            
                        }
                    }
                    
                    $result = $uploader->save($path, $_FILES['banner_name_tablet']['name'] );
                    $data['banner_name_tablet'] = $result['file'];
                } catch (Exception $e) {
                    $data['banner_name_tablet'] = $_FILES['banner_name_tablet']['name'];
                }
            }
            else {
                $bannerFile = $data['banner_name_tablet'];
                if ($bannerFile)
                    $data['banner_name_tablet'] = $bannerFile[0];
            }
            
            
            
            $model = Mage::getModel('ztheme/banner');        
            $model->setData($data)
                ->setId($this->getRequest()->getParam('banner_id'));
            
            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('ztheme')->__('Banner was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('banner_id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('banner_id' => $this->getRequest()->getParam('banner_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('ztheme')->__('Unable to find item to save')
        );
        $this->_redirect('*/*/');
    }
 
    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('banner_id') > 0) {
            try {
                $model = Mage::getModel('ztheme/banner');
                $model->setId($this->getRequest()->getParam('banner_id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Banner was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('banner_id' => $this->getRequest()->getParam('banner_id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction()
    {
        $zthemeIds = $this->getRequest()->getParam('ztheme');
        if (!is_array($zthemeIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($zthemeIds as $zthemeId) {
                    $ztheme = Mage::getModel('ztheme/banner')->load($zthemeId);
                    $ztheme->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                    count($zthemeIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * mass change status for item(s) action
     */
    public function massStatusAction()
    {
        $zthemeIds = $this->getRequest()->getParam('ztheme');
        if (!is_array($zthemeIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($zthemeIds as $zthemeId) {
                    Mage::getSingleton('ztheme/banner')
                        ->load($zthemeId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($zthemeIds))
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
    public function exportCsvAction()
    {
        $fileName   = 'ztheme.csv';
        $content    = $this->getLayout()
                           ->createBlock('ztheme/adminhtml_banner_grid')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'ztheme.xml';
        $content    = $this->getLayout()
                           ->createBlock('ztheme/adminhtml_banner_grid')
                           ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('ztheme');
    }
}