<?php
class Kronosav_Repair_Block_Adminhtml_Repair_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
    {
        parent::__construct();
        $this->setId('repair_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('repair')->__('News Information'));
    }
 
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('repair')->__('Repair Information'),
            'title'     => Mage::helper('repair')->__('Repair Information'),
            'content'   => $this->getLayout()->createBlock('repair/adminhtml_repair_edit_tab_form')->toHtml(),
        ));
       
        return parent::_beforeToHtml();
    }
}
