<?php
class MW_SocialGift_Block_Adminhtml_Quote_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'mw_socialgift';
        $this->_controller = 'adminhtml_quote';

        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->_removeButton('back');

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save Rule'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), 10);

        $this->_formScripts[] = " function saveAndContinueEdit(){editForm.submit($('edit_form').action + 'back/edit/') } ";

        $this->_formScripts[] =" Validation.add('mw_required', 'Please choose gift item for this rule!',{isNot : ''}); ";

        $model = Mage::registry('current_socialgift_quote_rule');

        $initData = array();
        if($model->getId()){
            $freeProducts = explode(",", $model->getData('gift_product_ids'));
            foreach($freeProducts as $product_id)
            {
                $initData[$product_id] = "cG9zaXRpb249";
            }
        }
        if(!sizeof($initData)) $initData="{}";
        else $initData = json_encode($initData);

        $this->_formScripts[] = "
            new serializerController(
                'rule_product_ids_tmp',
                ".$initData.", [\"position\"],
                ".Mage::app()->getLayout()->getBlock('socialgift_product_grid')->getJsObjectName().",
                'socialgifts'
            );
        ";
    }

    public function getHeaderText()
    {
        $rule = Mage::registry('current_socialgift_quote_rule');
        if ($rule->getRuleId()) {
            return Mage::helper('mw_socialgift')->__("Edit Rule '%s'", $this->htmlEscape($rule->getName()));
        }
        else {
            return Mage::helper('mw_socialgift')->__('New Rule');
        }
    }

    public function getProductsJson()
    {
        return '{}';
    }

	/**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
        return $this->getUrl('*/socialgift_quote/save');
    }
}
