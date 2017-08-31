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
 * Simipos Orders List Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_SimiPOS_Adminhtml_OrderController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('simipos/orders')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Sales Orders'),
                Mage::helper('adminhtml')->__('Sales Orders')
            );
        return $this;
    }
 
    /**
     * index action
     */
    public function indexAction()
    {
        $this->_title($this->__('SimiPOS'))
            ->_title($this->__('Sales Orders'));
        $this->_initAction()
            ->renderLayout();
    }
    
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'orders.csv';
        $grid       = $this->getLayout()->createBlock('simipos/adminhtml_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     * export grid item to XML Excel type
     */
    public function exportExcelAction()
    {
        $fileName   = 'orders.xml';
        $grid       = $this->getLayout()->createBlock('simipos/adminhtml_order_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simipos/orders');
    }
}
