<?php

class MW_FreeGift_Block_Adminhtml_Catalog_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'freegift';
        $this->_controller = 'adminhtml_catalog';
        
        $this->_updateButton('save', 'label', Mage::helper('catalogrule')->__('Save Rule'));
        $this->_updateButton('save', 'onclick', 'editForm.submit();');
        $this->_updateButton('delete', 'label', Mage::helper('catalogrule')->__('Delete Rule'));

        $rule = Mage::registry('current_gift_catalog_rule');

        if (!$rule->isDeleteable()) {
            $this->_removeButton('delete');
        }
		$this->_addButton('save_and_apply', array(
                'label'     => Mage::helper('catalogrule')->__('Save and Apply'),
                'onclick'   => "$('rule_auto_apply').value=1; editForm.submit();",
                'class' => 'save'
        ), 10);
        if (!$rule->isReadonly()) {
            $this->_addButton('save_and_continue', array(
                'label'     => Mage::helper('catalogrule')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class' => 'save'
            ), 10);
       $this->_formScripts[] = " function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'back/edit/') } ";
       $this->_formScripts[] ="
	        Validation.add('mw_required', 'Please choose gift item for this rule!',{isNot : ''});
	        ";
       $model = Mage::registry('current_gift_catalog_rule');
        
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
        //$this->setAdditionalJavaScript($script);
        } else {
            $this->_removeButton('reset');
            $this->_removeButton('save');
        }
    }

    public function getHeaderText()
    {
        if( Mage::registry('freegift_data') && Mage::registry('freegift_data')->getId() ) {
            return Mage::helper('freegift')->__("Edit Rule '%s'", $this->htmlEscape(Mage::registry('freegift_data')->getTitle()));
        } else {
            return Mage::helper('freegift')->__('Add Rule');
        }
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
        return $this->getUrl('*/freegift_catalog/save');
    }
}