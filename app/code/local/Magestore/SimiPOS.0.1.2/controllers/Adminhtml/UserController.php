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
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simipos User Controller
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Adminhtml_UserController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_SimiPOS_Adminhtml_UserController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('simipos/simipos')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Users Manager'),
                Mage::helper('adminhtml')->__('User Manager')
            );
        return $this;
    }
 
    /**
     * index (list users) action
     */
    public function indexAction()
    {
        $this->_title($this->__('SimiPOS'))
            ->_title($this->__('User Manager'));
        $this->_initAction()
            ->renderLayout();
    }
    
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $userId     = $this->getRequest()->getParam('id');
        $model      = Mage::getModel('simipos/user')->load($userId);

        if ($model->getId() || $userId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $data['user_id'] = $model->getId();
                $model->setData($data);
            }
            Mage::register('user_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('simipos/simipos');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('User Manager'),
                Mage::helper('adminhtml')->__('User Manager')
            );
            
            $this->_title($this->__('SimiPOS'))
                ->_title($this->__('User Manager'));
            if ($model->getId()) {
                $this->_title($this->__('Edit user "%s"', $model->getUsername()));
            } else {
                $this->_title($this->__('New User'));
            }

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('simipos/adminhtml_user_edit'))
                ->_addLeft($this->getLayout()->createBlock('simipos/adminhtml_user_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('simipos')->__('User does not exist')
            );
            $this->_redirect('*/*/');
        }
    }
 
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function ordersAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function exportOrdersCsvAction()
    {
        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('simipos/adminhtml_user_edit_tab_orders');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    
    public function exportOrdersExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('simipos/adminhtml_user_edit_tab_orders');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
 
    /**
     * save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $id    = $this->getRequest()->getParam('id');
            $model = Mage::getModel('simipos/user')->load($id);
            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This User no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
            $model->setData($data)->setId($id);
            
            /*
             * Unsetting new password and password confirmation if they are blank
             */
            if ($model->hasNewPassword() && $model->getNewPassword() === '') {
                $model->unsNewPassword();
            }
            if ($model->hasPasswordConfirmation() && $model->getPasswordConfirmation() === '') {
                $model->unsPasswordConfirmation();
            }
            
            $result = $model->validate();
            if (is_array($result)) {
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                foreach ($result as $message) {
                    Mage::getSingleton('adminhtml/session')->addError($message);
                }
                $this->_redirect('*/*/edit', array('_current' => true));
                return $this;
            }
            
            try {
                if ($model->getCreatedTime() == NULL) {
                    $model->setCreatedTime(now());
                }
                if (is_array($model->getStores())) {
                    $model->setStoreIds(implode(',', $model->getStores()));
                }
                $model->save();
                Mage::helper('simipos/magestore')->updateUser($model);
                
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('simipos')->__('User was successfully saved')
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
            Mage::helper('simipos')->__('Unable to find user to save')
        );
        $this->_redirect('*/*/');
    }
 
    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('simipos/user');
                $model->load($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::helper('simipos/magestore')->deleteUser($model);
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('User has been successfully deleted.')
                );
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
    public function massDeleteAction()
    {
        $simiposIds = $this->getRequest()->getParam('simipos');
        if (!is_array($simiposIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select user(s)'));
        } else {
            try {
                foreach ($simiposIds as $simiposId) {
                    $simipos = Mage::getModel('simipos/user')->load($simiposId);
                    $simipos->delete();
                    Mage::helper('simipos/magestore')->deleteUser($simipos);
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d user(s) were successfully deleted',
                    count($simiposIds))
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
        $simiposIds = $this->getRequest()->getParam('simipos');
        if (!is_array($simiposIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($simiposIds as $simiposId) {
                    $model = Mage::getSingleton('simipos/user')
                        ->load($simiposId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                    Mage::helper('simipos/magestore')->updateUser($model);
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d user(s) were successfully updated', count($simiposIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    public function massLocationAction()
    {
    	$simiposIds = $this->getRequest()->getParam('simipos');
        if (!is_array($simiposIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($simiposIds as $simiposId) {
                    Mage::getSingleton('simipos/user')
                        ->load($simiposId)
                        ->setLocationId($this->getRequest()->getParam('location'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d user(s) were successfully updated', count($simiposIds))
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
        $fileName   = 'users.csv';
        $content    = $this->getLayout()
                           ->createBlock('simipos/adminhtml_user_grid')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'users.xml';
        $content    = $this->getLayout()
                           ->createBlock('simipos/adminhtml_user_grid')
                           ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simipos/simipos');
    }
}
