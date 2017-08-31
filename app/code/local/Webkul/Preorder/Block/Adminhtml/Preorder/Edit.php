<?php

    class Webkul_Preorder_Block_Adminhtml_Preorder_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
        
        public function __construct() {
            parent::__construct();
            $this->_objectId = 'id';
            $this->_blockGroup = 'preorder';
            $this->_controller = 'adminhtml_preorder';
            $this->_updateButton('save', 'label', Mage::helper('preorder')->__('Save Item'));
            $this->_updateButton('delete', 'label', Mage::helper('preorder')->__('Delete Item'));
            $this->_addButton('saveandcontinue', array(
                'label'     =>     Mage::helper('adminhtml')->__('Save And Continue Edit'),
                'onclick'   =>     'saveAndContinueEdit()',
                'class'     =>     'save',
            ), -100);
            $this->_formScripts[] = "
                function toggleEditor() {
                    if (tinyMCE.getInstanceById('preorder_content') == null) {
                        tinyMCE.execCommand('mceAddControl', false, 'preorder_content');
                    } else {
                        tinyMCE.execCommand('mceRemoveControl', false, 'preorder_content');
                    }
                }

                function saveAndContinueEdit(){
                    editForm.submit($('edit_form').action+'back/edit/');
                }
            ";
        }

        public function getHeaderText() {
            if( Mage::registry('preorder_data') && Mage::registry('preorder_data')->getId() ) {
                return Mage::helper('preorder')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('preorder_data')->getTitle()));
            } else {
                return Mage::helper('preorder')->__('Add Item');
            }
        }
    }