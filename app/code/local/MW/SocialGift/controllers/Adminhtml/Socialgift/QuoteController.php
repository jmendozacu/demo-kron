<?php
class MW_SocialGift_Adminhtml_Socialgift_QuoteController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/socialgift/quote');
    }

    protected function _initRule()
    {
        $this->_title($this->__('Promotions'))->_title($this->__('Social Gift Pro'));

        Mage::register('current_socialgift_quote_rule', Mage::getModel('mw_socialgift/salesrule'));
        $id = (int)$this->getRequest()->getParam('id');

        if (!$id && $this->getRequest()->getParam('rule_id')) {
            $id = (int)$this->getRequest()->getParam('rule_id');
        }

        if ($id) {
            Mage::registry('current_socialgift_quote_rule')->load($id);
        }
    }

    public function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('mageworld/socialgift/quote')
            ->_addBreadcrumb(
                Mage::helper('mw_socialgift')->__('Social Gift'),
                Mage::helper('mw_socialgift')->__('Social Gift')
            )
        ;
        return $this;
    }

    /**
    * Index action
    */
    public function indexAction()
    {

        $model = Mage::getResourceModel('mw_socialgift/salesrule_collection');
        $model->getSelect()->columns('rule_id')->limit(1);
        $rule = $model->getData();
        if (count($rule) > 0) {
            $this->_redirect('adminhtml/socialgift_quote/edit/id/'.$rule[0]['rule_id'], array());
        }else{
            $this->_forward('edit');
        }
    }
    /**
    * Add News item action
    */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
    * Edit News item
    */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        
        $model = Mage::getModel('mw_socialgift/salesrule');

        if ($id) {
            $model->load($id);
            if (!$model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('salesrule')->__('This rule no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }
        
        $this->_title($model->getRuleId() ? $model->getName() : $this->__('New Rule'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(TRUE);
        if (!empty($data)) {
            $model->addData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('current_socialgift_quote_rule', $model);

        // 5. render breadcrumb & layout
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('salesrule')->__('Edit Rule')
                    : Mage::helper('salesrule')->__('New Rule'),
                $id ? Mage::helper('salesrule')->__('Edit Rule')
                    : Mage::helper('salesrule')->__('New Rule'))
            ->renderLayout();
    }

    public function saveAction(){
        if ($this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('mw_socialgift/salesrule');

                $data = $this->getRequest()->getPost();
                $data = $this->_filterDates($data, array('from_date', 'to_date'));
                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        Mage::throwException(Mage::helper('salesrule')->__('Wrong rule specified.'));
                    }
                }

                // Add
                if ($data['number_of_free_gift'] == "") {
                    $data['number_of_free_gift'] = 1;
                }
                $tmp = array();
                foreach (explode('&', $data['product_ids_tmp']) as $value) {
                    $_value = explode('=', $value);
                    $tmp[]  = $_value[0];
                }
                $data['gift_product_ids'] = implode(',', $tmp);
                if ($data['number_of_free_gift'] > count($tmp)) {
                    $this->_getSession()->addError(Mage::helper('catalogrule')->__('Number of free gift not greater than free gift items'));
                    $this->_redirect('*/*/edit', array(
                        'id' => $this->getRequest()->getParam('rule_id'),
                        '_current' => TRUE
                    ));
                    return;
                }
                // End

                $session = Mage::getSingleton('adminhtml/session');

                if (isset($data['simple_action']) && $data['simple_action'] == 'by_percent' && isset($data['discount_amount'])) {
                    $data['discount_amount'] = min(100, $data['discount_amount']);
                }

                $data['sg_customer_group_ids'] = ( isset($data["sg_customer_group_ids"]) ? implode(",", $data["sg_customer_group_ids"]) : "" );
                $data['sg_website_ids'] = ( isset($data["sg_website_ids"]) ? implode(",", $data["sg_website_ids"]) : "" );
                
                $all_countries = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
                unset($all_countries[0]);
                if ($data["all_allow_countries"] == '1') {
                    unset($data["sg_countries"]);
                    $data["sg_countries"] = array();
                    foreach ($all_countries as $key => $value) {
                        $data["sg_countries"][] = $value['value'];
                    }
                }

                $data['sg_countries'] = ( isset($data["sg_countries"]) ? implode(",", $data["sg_countries"]) : "" );

                if (!$data['from_date'])
                    $data['from_date'] = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));

                $model->setData($data);
                $session->setPageData($model->getData());
                $model->save();

                $session->addSuccess(Mage::helper('salesrule')->__('The rule has been saved.'));
                $session->setPageData(FALSE);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array(
                        'id' => $model->getId()
                    ));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('rule_id');
                if (!empty($id)) {
                    $this->_redirect('*/*/edit', array('id' => $id));
                } else {
                    $this->_redirect('*/*/new');
                }
                return;
            }
            catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('catalogrule')->__('An error occurred while saving the rule data. Please review the log and try again.'));
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array(
                    'id' => $this->getRequest()->getParam('rule_id')
                ));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('mw_socialgift/salesrule');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('salesrule')->__('The rule has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('catalogrule')->__('An error occurred while deleting the rule. Please review the log and try again.'));
                Mage::logException($e);
                $this->_redirect('*/*/edit', array(
                    'id' => $this->getRequest()->getParam('id')
                ));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('salesrule')->__('Unable to find a rule to delete.'));
        $this->_redirect('*/*/');
    }


    public function gridAction()
    {
        $this->_initRule()->loadLayout()->renderLayout();
    }

    public function freeproductAction()
    {
        $id        = $this->getRequest()->getParam('id');
        $model     = Mage::getModel('mw_socialgift/salesrule')->load($id);
        $socialgifts = $this->getRequest()->getParam('socialgifts');
        Mage::register('current_socialgift_quote_rule', $model);
        $block = $this->getLayout()->createBlock('mw_socialgift/adminhtml_quote_edit_tab_gift_socialgift', 'socialgift_product_grid')->setSocialGifts($socialgifts);
        $this->getResponse()->setBody($block->toHtml());
    }
}