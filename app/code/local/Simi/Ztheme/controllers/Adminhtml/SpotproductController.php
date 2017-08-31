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
 */
class Simi_Ztheme_Adminhtml_SpotproductController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('ztheme/spotproduct')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
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
        $zthemeId     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('ztheme/spotproduct')->load($zthemeId);

        if ($model->getId() || $zthemeId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('spot_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('ztheme/spotproduct');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item News'),
                Mage::helper('adminhtml')->__('Item News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('ztheme/adminhtml_spot_edit'))
                ->_addLeft($this->getLayout()->createBlock('ztheme/adminhtml_spot_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('ztheme')->__('Item does not exist')
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

            $image_type='spotproduct';
            $image_type_id=$this->getRequest()->getParam('id');
            $storeId = Mage::app()->getStore()->getStoreId();
            if($image_type_id==null) $image_type_id=1;
            
            if (isset($_FILES['spotproduct_banner_name']['name']) && $_FILES['spotproduct_banner_name']['name'] != '') {
                try {
                    /* Starting upload */    
                    $uploader = new Varien_File_Uploader('spotproduct_banner_name');
                    
                    // Any extention would work
                       $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    
                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //    (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);
                    
                    // We set media as the upload dir
                    str_replace(" ", "_", $_FILES['spotproduct_banner_name']['name']);                    
                    $website = $data['website_id'];
                    $website= $data['website_id'];
                   if($website==null) $website=0;
                    $path = Mage::getBaseDir('media') . DS . 'simi' . DS . 'ztheme' . DS . 'spotbanner';
                    if (!is_dir($path)) {
                        try {
                            mkdir($path, 0777, TRUE);
                        } catch (Exception $e) {
                            
                        }
                    }
                    
                    $result = $uploader->save($path, $_FILES['spotproduct_banner_name']['name'] );
                    $data['spotproduct_banner_name'] = $result['file'];
                } catch (Exception $e) {
                    $data['spotproduct_banner_name'] = $_FILES['spotproduct_banner_name']['name'];
                }
            }
            else {
                $bannerFile = $data['spotproduct_banner_name'];
                if ($bannerFile)
                    $data['spotproduct_banner_name'] = $bannerFile[0];
            }
            
            if (isset($_FILES['spotproduct_banner_name_tablet']['name']) && $_FILES['spotproduct_banner_name_tablet']['name'] != '') {
                try {
                    /* Starting upload */    
                    $uploader = new Varien_File_Uploader('spotproduct_banner_name_tablet');
                    
                    // Any extention would work
                       $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    
                    // Set the file upload mode 
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders 
                    //    (file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);
                    
                    // We set media as the upload dir
                    str_replace(" ", "_", $_FILES['spotproduct_banner_name_tablet']['name']);                    
                    $website = $data['website_id'];
                    $website= $data['website_id'];
                   if($website==null) $website=0;
                    $path = Mage::getBaseDir('media') . DS . 'simi' . DS . 'ztheme' . DS . 'spotbanner_tab';
                    if (!is_dir($path)) {
                        try {
                            mkdir($path, 0777, TRUE);
                        } catch (Exception $e) {
                            
                        }
                    }
                    
                    $result = $uploader->save($path, $_FILES['spotproduct_banner_name_tablet']['name'] );
                    $data['spotproduct_banner_name_tablet'] = $result['file'];
                } catch (Exception $e) {
                    $data['spotproduct_banner_name_tablet'] = $_FILES['spotproduct_banner_name_tablet']['name'];
                }
            }
            else {
                $bannerFile = $data['spotproduct_banner_name_tablet'];
                if ($bannerFile)
                    $data['spotproduct_banner_name_tablet'] = $bannerFile[0];
            }
            
              
            $model = Mage::getModel('ztheme/spotproduct')->load($image_type_id);
            $key=$model->getData('spotproduct_key');
            
            $model->setData($data)
                ->setId($image_type_id);
            $model->setData('spotproduct_key',$key);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('ztheme')->__('Banner was successfully saved')
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
            Mage::helper('ztheme')->__('Unable to find item to save')
        );
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('ztheme');
    }
}