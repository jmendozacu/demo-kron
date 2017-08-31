<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('groupdeals')->__('Continue'),
                    'onclick'   => "setDealSettings('".$this->getContinueUrl()."','product_type')",
                    'class'     => 'save'
                    ))
                );
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('groupdeals')->__('Create Deal Settings')));

        $fieldset->addField('product_type', 'select', array(
            'label' => Mage::helper('groupdeals')->__('Deal Type'),
            'title' => Mage::helper('groupdeals')->__('Deal Type'),
            'name'  => 'type',
            'value' => '',
            'values'=> Mage::getModel('groupdeals/source_type')->getOptionArray()
        ));

        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        $this->setForm($form);
    }

    public function getContinueUrl()
    {
    	$attributeSetId = Mage::helper('groupdeals')->getGroupdealAttributeSetId();
        return $this->getUrl('*/*/new', array(
            '_current'  => true,
            'set'       => $attributeSetId,
            'type'      => '{{type}}'
        ));
    }    
    
}
