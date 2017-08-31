<?php
  
  class Webkul_Preorder_Block_Adminhtml_Preorder_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('preorder_form', array('legend'=>Mage::helper('preorder')->__('Item information')));
       
        $fieldset->addField('title', 'text', array(
            'label'     =>    Mage::helper('preorder')->__('Title'),
            'class'     =>    'required-entry',
            'required'  =>    true,
            'name'      =>    'title',
        ));

        $fieldset->addField('filename', 'file', array(
            'label'     =>    Mage::helper('preorder')->__('File'),
            'required'  =>    false,
            'name'      =>    'filename',
        ));

        $fieldset->addField('status', 'select', array(
            'label'     =>    Mage::helper('preorder')->__('Status'),
            'name'      =>    'status',
            'values'    =>    array(
                                array(
                                    'value'     =>    1,
                                    'label'     =>    Mage::helper('preorder')->__('Enabled'),
                                ),
                                array(
                                    'value'     =>    2,
                                    'label'     =>    Mage::helper('preorder')->__('Disabled'),
                                ),
                              ),
        ));
         
        $fieldset->addField('content', 'editor', array(
            'name'      =>    'content',
            'label'     =>    Mage::helper('preorder')->__('Content'),
            'title'     =>    Mage::helper('preorder')->__('Content'),
            'style'     =>    'width:700px; height:500px;',
            'wysiwyg'   =>    false,
            'required'  =>    true,
        ));
         
        if ( Mage::getSingleton('adminhtml/session')->getPreorderData() ) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getPreorderData());
            Mage::getSingleton('adminhtml/session')->setPreorderData(null);
        } elseif ( Mage::registry('preorder_data') ) {
            $form->setValues(Mage::registry('preorder_data')->getData());
        }
        return parent::_prepareForm();
    }
  }