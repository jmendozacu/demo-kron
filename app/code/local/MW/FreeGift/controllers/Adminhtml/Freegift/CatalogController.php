<?php
class MW_FreeGift_Adminhtml_Freegift_CatalogController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/freegift/catalog');
    }

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('mageworld/freegift/catalog')->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Catalog Rules Manager'));
        
        return $this;
    }
    
    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }
    
    public function editAction()
    {
        $id    = $this->getRequest()->getParam('id');
        $model = $model = Mage::getModel('freegift/rule');
        if ($id) {
            $model->load($id);
            if (!$model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalogrule')->__('This rule no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }

        if ($model->getId() || $id == 0) {
            Mage::register('current_gift_catalog_rule', $model);
            
            $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
            
            $this->_initAction();
            
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog Rules Manager'), Mage::helper('adminhtml')->__('Catalog Rules Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Catalog Rules News'), Mage::helper('adminhtml')->__('Catalog Rules News'));
            
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('freegift')->__('The rule does not exist'));
            $this->_redirect('*/*/');
        }
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('freegift/rule');
                $data  = $this->getRequest()->getPost();
                $data  = $this->_filterDates($data, array(
                    'from_date',
                    'to_date'
                ));
                $tmp   = array();
                foreach (explode('&', $data['product_ids_tmp']) as $value) {
                    $_value = explode('=', $value);
                    if ($_value[0])
                        $tmp[] = $_value[0];
                }
                $data['gift_product_ids'] = implode(',', $tmp);
                
                $validateResult = $model->validateData(new Varien_Object($data));
                
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->_redirect('*/*/edit', array(
                        'id' => $model->getId()
                    ));
                    return;
                }
                /* Add new feature Buy X get Y - 17/12/13 */
                $custom_cdn['buy_x_get_y']['bx'] = ($this->getRequest()->getPost('buy_x')) ? $this->getRequest()->getPost('buy_x') : 1;
                $custom_cdn['buy_x_get_y']['gy'] = ($this->getRequest()->getPost('get_y')) ? $this->getRequest()->getPost('get_y') : 1;
                $custom_cdn = serialize($custom_cdn);
                /* [bX-gY]*/
                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
                
                if (!empty($data['auto_apply'])) {
                    $autoApply = true;
                    unset($data['auto_apply']);
                } else {
                    $autoApply = false;
                }
                
                if (!$data['from_date'])
                    $data['from_date'] = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
                $model->loadPost($data);
                $model->setData('from_date', $data['from_date']);
                $model->setData('to_date', $data['to_date']);
                $model->setData('condition_customized', $custom_cdn);

                Mage::getSingleton('adminhtml/session')->setPageData($model->getData());
                
                $model->save();
                
                // Fix: can't save customer_group_ids and website_ids
                $customer_group_ids = "";
                $website_ids        = "";
                if(isset($data["customer_group_ids"]))
                    $customer_group_ids = implode(",", $data["customer_group_ids"]);
                if(isset($data["website_ids"]))
                    $website_ids = implode(",", $data["website_ids"]);
                
                $conn     = Mage::getModel('core/resource')->getConnection('core_write');
                $resource = Mage::getModel('freegift/rule')->getCollection();
                $sql      = "UPDATE {$resource->getTable('rule')} SET website_ids='{$website_ids}',customer_group_ids='{$customer_group_ids}' WHERE rule_id='{$model->getId()}'";
                $conn->query($sql);
                
                if ($this->getRequest()->getPost('auto_apply')) {
                    $model->load($model->getId());
                    $this->_removeOlderData($model->getId());
                    if (($model->getIsActive() == MW_FreeGift_Model_Status::STATUS_ENABLED)) {
                        $matchingProductIds = $model->getMatchingProductIds();
                        foreach ($matchingProductIds as $product_id) {
                            $freegiftProduct = Mage::getModel('freegift/product');
                            $data = array(
                                'rule_id' => $model->getId(),
                                'product_id' => $product_id,
                                'from_date' => $model->getData('from_date'),
                                'to_date' => $model->getData('to_date'),
                                'stop_rules_processing' => $model->getData('stop_rules_processing'),
                                //'customer_group_ids'=> implode(",",$model->getData('customer_group_ids')),
                                'customer_group_ids' => $customer_group_ids,
                                'gift_product_ids' => $model->getData('gift_product_ids'),
                                //'website_ids'		=> implode(",",$model->getData('website_ids')),
                                'website_ids' => $website_ids,
                                'discount_qty' => $model->getData('discount_qty'),
                                'times_used' => $model->getData('times_used'),
                                'sort_order' => $model->getData('sort_order')
                            );
                            $freegiftProduct->setData($data)->save();
                        }
                    }
                }
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array(
                        'id' => $model->getId()
                    ));
                    return;
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('freegift')->__('The rule has been saved.'));
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('freegift')->__('An error occurred while saving the rule data. Please review the log and try again.'));
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
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('freegift/rule');
                
                $model->setId($this->getRequest()->getParam('id'))->delete();
                
                
                $conn     = Mage::getModel('core/resource')->getConnection('core_write');
                $resource = Mage::getModel('freegift/product')->getCollection();
                $sql      = "DELETE FROM {$resource->getTable('product')}  WHERE rule_id='{$this->getRequest()->getParam('id')}'";
                $conn->query($sql);
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The rule was successfully deleted'));
                $this->_redirect('*/*/');
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array(
                    'id' => $this->getRequest()->getParam('id')
                ));
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function freeproductAction()
    {
        $id    = $this->getRequest()->getParam('id');
        $model = Mage::getModel('freegift/rule')->load($id);
        Mage::register('current_gift_catalog_rule', $model);
        $freegifts = $this->getRequest()->getParam('freegifts');
        $block     = $this->getLayout()->createBlock('freegift/adminhtml_catalog_edit_tab_actions_freegift', 'freegift_product_grid')->setFreeGifts($freegifts);
        $this->getResponse()->setBody($block->toHtml());
    }
    
    protected function _removeOlderData($rule_id = null)
    {
        $collection = Mage::getModel('freegift/product')->getCollection();
        if ($rule_id)
            $collection->addFieldToFilter('rule_id', $rule_id);
        foreach ($collection as $freegiftProduct) {
            $freegiftProduct->delete();
        }
    }
    public function applyrulesAction()
    {
        $this->_removeOlderData();
        $collection = Mage::getResourceModel('freegift/rule_collection');
        $collection->getSelect()->where('is_active=1');
        $collection->getSelect()->where('((discount_qty > times_used) or (discount_qty=0))');
        $collection->getSelect()->order('sort_order');
        $collection->load();
        
        foreach ($collection as $rule) {
            $rule->load($rule->getId());
            $matchingProductIds = $rule->getMatchingProductIds();
            foreach ($matchingProductIds as $product_id) {
                $freegiftProduct = Mage::getModel('freegift/product');
                $data            = array(
                    'rule_id' => $rule->getId(),
                    'product_id' => $product_id,
                    'from_date' => $rule->getData('from_date'),
                    'to_date' => $rule->getData('to_date'),
                    'stop_rules_processing' => $rule->getData('stop_rules_processing'),
                    //'customer_group_ids'=> implode(",",$rule->getData('customer_group_ids')),
                    'customer_group_ids' => $rule->getData('customer_group_ids'),
                    'gift_product_ids' => $rule->getData('gift_product_ids'),
                    //'website_ids'        => implode(",",$rule->getData('website_ids')),
                    'website_ids' => $rule->getData('website_ids'),
                    'discount_qty' => $rule->getData('discount_qty'),
                    'times_used' => $rule->getData('times_used'),
                    'sort_order' => $rule->getData('sort_order')
                );
                $freegiftProduct->setData($data)->save();
            }
        }
        
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The rules have been applied.'));
        $this->_redirect('*/*/');
    }
}