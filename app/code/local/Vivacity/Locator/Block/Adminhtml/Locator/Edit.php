<?php
class Vivacity_Locator_Block_Adminhtml_Locator_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{
   public function __construct()
   {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'locator';
        $this->_controller = 'adminhtml_locator';
        $this->_updateButton('save', 'label','Save Store');
        $this->_updateButton('delete', 'label', 'Delete Store');
    }
       
    public function getHeaderText()
    {
        if( Mage::registry('locator_data') && Mage::registry('locator_data')->getId())
         {
              return $this->htmlEscape(Mage::registry('locator_data')->getTitle());
         }
         else
         {
             return 'Add a Store';
         }
    }
}
