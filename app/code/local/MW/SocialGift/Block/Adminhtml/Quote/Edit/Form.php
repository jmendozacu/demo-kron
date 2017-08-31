<?php
class MW_SocialGift_Block_Adminhtml_Quote_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('socialgift_quote_form');
        $this->setTitle(Mage::helper('mw_socialgift')->__('Rule Information'));
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'),'enctype' => 'multipart/form-data', 'method' => 'post'));
        $form->setUseContainer(TRUE);
        $this->setForm($form);
        return parent::_prepareForm();
    }


}
