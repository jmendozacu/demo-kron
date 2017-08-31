<?php
    class MW_FreeGift_Adminhtml_FreeGift_ReportController extends Mage_Adminhtml_Controller_Action
    {
        protected function _isAllowed()
        {
            return Mage::getSingleton('admin/session')->isAllowed('promo/freegift/report');
        }

        protected function _initAction()
        {
            $this->loadLayout()
                ->_setActiveMenu('promo/freegift')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

            return $this;
        }
        public function dashboardAction()
        {
            if($this->getRequest()->getPost('ajax') == 'true'){
                $data = $this->getRequest()->getPost();
                switch($this->getRequest()->getPost('type'))
                {
                    case 'dashboard':
                        print Mage::getModel('freegift/report')->prepareCollection($data);
                        break;
                }
                exit;
            }
            $this->_title($this->__('Reports'))
                ->_title($this->__('Result'))
                ->_title($this->__(FreeGift));

            $this->_initAction()
                ->_setActiveMenu('promo/freegift')
                ->_addBreadcrumb(Mage::helper('freegift')->__('Report FreeGift'), Mage::helper('freegift')->__('FreeGift Dashboard'))
                ->_addContent($this->getLayout()->createBlock('freegift/adminhtml_report_dashboard'))
                ->renderLayout();
        }
    }