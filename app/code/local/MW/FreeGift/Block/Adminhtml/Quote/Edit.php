<?php
class MW_FreeGift_Block_Adminhtml_Quote_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'freegift';
        $this->_controller = 'adminhtml_quote';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('freegift')->__('Save Rule'));
        $this->_updateButton('save', 'onclick', 'editForm.submit();');
        $this->_updateButton('delete', 'label', Mage::helper('freegift')->__('Delete Rule'));

        $rule = Mage::registry('current_freegift_quote_rule');

        if (!$rule->isDeleteable()) {
            $this->_removeButton('delete');
        }

        if ($rule->isReadonly()) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
        } else {
            $this->_addButton('save_and_continue', array(
                'label'     => Mage::helper('freegift')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class' => 'save'
            ), 10);
            
            $this->_formScripts[] = " function saveAndContinueEdit(){editForm.submit($('edit_form').action + 'back/edit/') } ";
	        $this->_formScripts[] ="
	        Validation.add('mw_required', 'Please choose gift item for this rule!',{isNot : ''});
	        ";
            $model = Mage::registry('current_freegift_quote_rule');
	        
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
	        $this->_formScripts[] = "new serializerController('rule_product_ids_tmp', ".$initData.", [\"position\"], ".Mage::app()->getLayout()->getBlock('freegift_product_grid')->getJsObjectName().", 'freegifts');";
        }    
    }

    public function getHeaderText()
    {
        $rule = Mage::registry('current_freegift_quote_rule');
        if ($rule->getRuleId()) {
            return Mage::helper('freegift')->__("Edit Rule '%s'", $this->htmlEscape($rule->getName()));
        }
        else {
            return Mage::helper('freegift')->__('New Rule');
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
        return $this->getUrl('*/freegift_quote/save');
    }
}
