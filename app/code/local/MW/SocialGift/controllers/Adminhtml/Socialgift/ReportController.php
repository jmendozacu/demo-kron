<?php
    class MW_SocialGift_Adminhtml_SocialGift_ReportController extends Mage_Adminhtml_Controller_Action
    {
        protected function _isAllowed()
        {
            return Mage::getSingleton('admin/session')->isAllowed('promo/socialgift/report');
        }

        protected function _initAction()
        {
            $this->loadLayout()
                ->_setActiveMenu('promo/mw_socialgift')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

            return $this;
        }
        public function dashboardAction()
        {
            if($this->getRequest()->getPost('ajax') == 'TRUE'){
                $data = $this->getRequest()->getPost();
                switch($this->getRequest()->getPost('type'))
                {
                    case 'dashboard':
                        print Mage::getModel('mw_socialgift/report')->prepareCollection($data);
                        break;
                }
                exit;
            }
            $this->_title($this->__('Reports'))
                ->_title($this->__('Result'))
                ->_title($this->__('mw_socialgift'));

            $this->_initAction()
                ->_setActiveMenu('promo/mw_socialgift')
                ->_addBreadcrumb(Mage::helper('mw_socialgift')->__('Report SocialGift'), Mage::helper('mw_socialgift')->__('SocialGift Dashboard'))
                ->_addContent($this->getLayout()->createBlock('mw_socialgift/adminhtml_report_dashboard'))
                ->renderLayout();
        }
    } 