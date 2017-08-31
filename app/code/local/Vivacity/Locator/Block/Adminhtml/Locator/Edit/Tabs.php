<?php
  class Vivacity_Locator_Block_Adminhtml_Locator_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
  {
     public function __construct()
     {
          parent::__construct();
          $this->setId('locator_tabs');
          $this->setDestElementId('edit_form');
         // $this->setTitle('Information sur le contact');
      }
      protected function _beforeToHtml()
      {
          $this->addTab('form_section', array(
                   'label' => 'Store Locator Information',
                   'title' => 'Store Locator Information',
                   'content' => $this->getLayout()
     ->createBlock('locator/adminhtml_locator_edit_tab_form')
     ->toHtml()
         ));
         return parent::_beforeToHtml();
    }
}
