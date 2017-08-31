<?php

  class Webkul_Preorder_Block_Adminhtml_Preorder_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('preorder_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('preorder')->__('Item Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label'     =>    Mage::helper('preorder')->__('Item Information'),
            'title'     =>    Mage::helper('preorder')->__('Item Information'),
            'content'   =>    $this->getLayout()->createBlock('preorder/adminhtml_preorder_edit_tab_form')->toHtml(),
        ));
       
        return parent::_beforeToHtml();
    }
  }