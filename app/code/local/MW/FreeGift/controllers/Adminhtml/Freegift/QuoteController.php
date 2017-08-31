<?php
class MW_FreeGift_Adminhtml_Freegift_QuoteController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/freegift/quote');
    }

    protected function _initRule()
    {
        $this->_title($this->__('Promotions'))->_title($this->__('Shopping Cart Price Rules'));

        Mage::register('current_freegift_quote_rule', Mage::getModel('freegift/salesrule'));

        $id = $this->getRequest()->getParam('id');
        if (!$id && $this->getRequest()->getParam('rule_id')) {
            $id = (int) $this->getRequest()->getParam('rule_id');
        }

        if ($id) {
            Mage::registry('current_freegift_quote_rule')->load($id);
        }
    }

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('mageworld/freegift/quote')->_addBreadcrumb(Mage::helper('salesrule')->__('Free Gift'), Mage::helper('salesrule')->__('Free Gift'));
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Promotions'))->_title($this->__('Shopping Cart Free Gift Rules'));

        $this->_initAction()->_addBreadcrumb(Mage::helper('salesrule')->__('Free Gift'), Mage::helper('salesrule')->__('Free Gift'))->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id    = $this->getRequest()->getParam('id');
        $model = Mage::getModel('freegift/salesrule');

        if ($id) {
            $model->load($id);
            if (!$model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('salesrule')->__('This rule no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }

        $this->_title($model->getRuleId() ? $model->getName() : $this->__('New Rule'));

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        Mage::register('current_freegift_quote_rule', $model);

        $this->_initAction()->getLayout()->getBlock('freegift_quote_edit')->setData('action', $this->getUrl('*/*/save'));

        $this->_addBreadcrumb($id ? Mage::helper('freegift')->__('Edit Rule') : Mage::helper('freegift')->__('New Rule'), $id ? Mage::helper('freegift')->__('Edit Rule') : Mage::helper('freegift')->__('New Rule'))->renderLayout();

    }
    public function saveAction()    {
        if ($this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('freegift/salesrule');
                $data  = $this->getRequest()->getPost();

                if ($data['number_of_free_gift'] == "") {
                    $data['number_of_free_gift'] = 1;
                }
                $data = $this->_filterDates($data, array(
                    'from_date',
                    'to_date'
                ));

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
                        '_current' => true
                    ));
                    return;
                }

                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        Mage::throwException(Mage::helper('salesrule')->__('Wrong rule specified.'));
                    }
                }

                $session = Mage::getSingleton('adminhtml/session');

                $validateResult = $model->validateData(new Varien_Object($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $session->addError($errorMessage);
                    }
                    $session->setPageData($data);
                    $this->_redirect('*/*/edit', array(
                        'id' => $model->getId()
                    ));
                    return;
                }

                if (isset($data['simple_action']) && $data['simple_action'] == 'by_percent' && isset($data['discount_amount'])) {
                    $data['discount_amount'] = min(100, $data['discount_amount']);
                }
                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }

                //Upload image
                if ((isset($_FILES['promotion_banner']['name'])) and (file_exists($_FILES['promotion_banner']['tmp_name']))) {
                    try {
                        $uploader = new Varien_File_Uploader('promotion_banner');

                        $uploader->setAllowedExtensions(array(
                            'jpg',
                            'jpeg',
                            'gif',
                            'png'
                        ));
                        $uploader->setAllowRenameFiles(false);
                        $ext = explode(".", $_FILES['promotion_banner']['name']);
                        $uploader->setFilesDispersion(false);
                        $path = Mage::getBaseDir('media') . DS . 'promotionbanner';
                        $uploader->save($path, md5($data['name']).".".end($ext));

                    }catch (Exception $e) {
                    }

                    $data['promotion_banner'] = 'promotionbanner/' . md5($data['name']).".".end($ext);

                } else {
                    if (isset($data['promotion_banner']['delete']) && $data['promotion_banner']['delete'] == 1) {
                        $data['promotion_banner'] = '';
                    } else {
                        unset($data['promotion_banner']);
                    }
                }

                unset($data['rule']);
                if (!$data['from_date'])
                    $data['from_date'] = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
                $model->loadPost($data);

                $useAutoGeneration = (int) !empty($data['use_auto_generation']);

                $model->setUseAutoGeneration($useAutoGeneration);
                if ($useAutoGeneration == 1 || $data['coupon_type'] == 1) {
                    $model->setData('coupon_code', "");
                } else {
                    $model->setData('coupon_code', $data['coupon_code']);
                }

                $model->setData('from_date', $data['from_date']);
                $model->setData('to_date', $data['to_date']);
                $session->setPageData($model->getData());

                $model->save();

                // Fix: can't save customer_group_ids and website_ids
                $customer_group_ids = "";
                $website_ids        = "";
                if (isset($data["customer_group_ids"])) {
                    $customer_group_ids = implode(",", $data["customer_group_ids"]);
                }
                if (isset($data["website_ids"])) {
                    $website_ids = implode(",", $data["website_ids"]);
                }

                $conn     = Mage::getModel('core/resource')->getConnection('core_write');
                $resource = Mage::getModel('freegift/salesrule')->getCollection();
                $sql      = "UPDATE {$resource->getTable('salesrule')} SET website_ids='{$website_ids}',customer_group_ids='{$customer_group_ids}' WHERE rule_id='{$model->getId()}'";
                $conn->query($sql);

                $session->addSuccess(Mage::helper('salesrule')->__('The rule has been saved.'));
                $session->setPageData(false);
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
                $model = Mage::getModel('freegift/salesrule');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('salesrule')->__('The rule has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('catalogrule')->__('An error occurred while deleting the rule. Please review the log and try again.'));
                Mage::logException($e);
                $this->_redirect('*/*/edit', array(
                    'id' => $this->getRequest()->getParam('id')
                ));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('salesrule')->__('Unable to find a rule to delete.'));
        $this->_redirect('*/*/');
    }

    /**
     * Coupons mass delete action
     */
    public function couponsMassDeleteAction()
    {
        $this->_initRule();
        $rule = Mage::registry('current_freegift_quote_rule');

        if (!$rule->getId()) {
            $this->_forward('noRoute');
        }

        $codesIds = $this->getRequest()->getParam('ids');
        if (is_array($codesIds)) {
            $couponsCollection = Mage::getResourceModel('freegift/coupon_collection')->addFieldToFilter('coupon_id', array(
                'in' => $codesIds
            ));

            foreach ($couponsCollection as $coupon) {
                $coupon->delete();
            }
        }
    }


    /**
     * Generate Coupons action
     */
    public function generateAction()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noRoute');
            return;
        }
        $result = array();
        $this->_initRule();

        /** @var $rule Mage_SalesRule_Model_Rule */
        $rule = Mage::registry('current_freegift_quote_rule');

        if (!$rule->getId()) {
            $result['error'] = Mage::helper('freegift')->__('Rule is not defined');
        } else {
            try {
                $data = $this->getRequest()->getParams();
                if (!empty($data['to_date'])) {
                    $data = array_merge($data, $this->_filterDates($data, array(
                        'to_date'
                    )));
                }

                /** @var $generator Mage_SalesRule_Model_Coupon_Massgenerator */
                $generator = $rule->getCouponMassGenerator();
                if (!$generator->validateData($data)) {
                    $result['error'] = Mage::helper('freegift')->__('Not valid data provided');
                } else {
                    $generator->setData($data);
                    $generator->generatePool();
                    $generated = $generator->getGeneratedCount();
                    $this->_getSession()->addSuccess(Mage::helper('freegift')->__('%s Coupon(s) have been generated', $generated));
                    $this->_initLayoutMessages('adminhtml/session');
                    $result['messages'] = $this->getLayout()->getMessagesBlock()->getGroupedHtml();
                }
            }
            catch (Mage_Core_Exception $e) {
                $result['error'] = $e->getMessage();
            }
            catch (Exception $e) {
                $result['error'] = Mage::helper('freegift')->__('An error occurred while generating coupons. Please review the log and try again.');
                Mage::logException($e);
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function gridAction()
    {
        $this->_initRule()->loadLayout()->renderLayout();
    }

    /**
     * Chooser source action
     */
    public function chooserAction()
    {
        $uniqId       = $this->getRequest()->getParam('uniq_id');
        $chooserBlock = $this->getLayout()->createBlock('adminhtml/promo_widget_chooser', '', array(
            'id' => $uniqId
        ));
        $this->getResponse()->setBody($chooserBlock->toHtml());
    }

    /**
     * Coupon codes grid
     */
    public function couponsGridAction()
    {
        $this->_initRule();
        $this->loadLayout()->renderLayout();
    }

    /**
     * Export coupon codes as excel xml file
     *
     * @return void
     */
    public function exportCouponsXmlAction()
    {
        $this->_initRule();
        $rule = Mage::registry('current_freegift_quote_rule');
        if ($rule->getId()) {
            $fileName = 'coupon_codes.xml';
            $content  = $this->getLayout()->createBlock('freegift/adminhtml_quote_edit_tab_coupons_grid')->getExcelFile($fileName);
            $this->_prepareDownloadResponse($fileName, $content);
        } else {
            $this->_redirect('*/*/detail', array(
                '_current' => true
            ));
            return;
        }
    }

    /**
     * Export coupon codes as CSV file
     *
     * @return void
     */
    public function exportCouponsCsvAction()
    {
        $this->_initRule();
        $rule = Mage::registry('current_freegift_quote_rule');
        if ($rule->getId()) {
            $fileName = 'coupon_codes.csv';
            $content  = $this->getLayout()->createBlock('freegift/adminhtml_quote_edit_tab_coupons_grid')->getCsvFile();
            $this->_prepareDownloadResponse($fileName, $content);
        } else {
            $this->_redirect('*/*/detail', array(
                '_current' => true
            ));
            return;
        }
    }

    public function freeproductAction()
    {
        $id        = $this->getRequest()->getParam('id');
        $model     = Mage::getModel('freegift/salesrule')->load($id);
        $freegifts = $this->getRequest()->getParam('freegifts');
        Mage::register('current_freegift_quote_rule', $model);
        $block = $this->getLayout()->createBlock('freegift/adminhtml_quote_edit_tab_actions_freegift', 'freegift_product_grid')->setFreeGifts($freegifts);
        $this->getResponse()->setBody($block->toHtml());
    }
}