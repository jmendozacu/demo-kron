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
 * Simipos Location Controller
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Adminhtml_LocationController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_SimiPOS_Adminhtml_LocationController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('simipos/location')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Locations Manager'),
                Mage::helper('adminhtml')->__('Location Manager')
            );
        return $this;
    }
 
    /**
     * index (list locations) action
     */
    public function indexAction()
    {
        $this->_title($this->__('SimiPOS'))
            ->_title($this->__('Location Manager'));
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
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('simipos/location')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $data['location_id'] = $model->getId();
                $model->setData($data);
            }
            Mage::register('location_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('simipos/location');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Location Manager'),
                Mage::helper('adminhtml')->__('Location Manager')
            );
            
            $this->_title($this->__('SimiPOS'))
                ->_title($this->__('Location Manager'));
            if ($model->getId()) {
                $this->_title($this->__('Edit location "%s"', $model->getName()));
            } else {
                $this->_title($this->__('New Location'));
            }

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('simipos/adminhtml_location_edit'))
                ->_addLeft($this->getLayout()->createBlock('simipos/adminhtml_location_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('simipos')->__('Location does not exist')
            );
            $this->_redirect('*/*/');
        }
    }
 
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function usersAction()
    {
    	$this->loadLayout();
    	$this->renderLayout();
    }
    
    public function ordersAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function exportOrdersCsvAction()
    {
        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('simipos/adminhtml_location_edit_tab_orders');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    
    public function exportOrdersExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('simipos/adminhtml_location_edit_tab_orders');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
 
    /**
     * save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $id    = $this->getRequest()->getParam('id');
            $model = Mage::getModel('simipos/location')->load($id);
            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This location no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
            $model->setData($data)->setId($id);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('simipos')->__('Location was successfully saved')
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
            Mage::helper('simipos')->__('Unable to find location to save')
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
                $model = Mage::getModel('simipos/location');
                $model->load($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Location has been successfully deleted.')
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
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select location(s)'));
        } else {
            try {
                foreach ($simiposIds as $simiposId) {
                    $simipos = Mage::getModel('simipos/location')->load($simiposId);
                    $simipos->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d location(s) were successfully deleted',
                    count($simiposIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'locations.csv';
        $content    = $this->getLayout()
                           ->createBlock('simipos/adminhtml_location_grid')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'locations.xml';
        $content    = $this->getLayout()
                           ->createBlock('simipos/adminhtml_location_grid')
                           ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simipos/location');
    }
}
