<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_ThemeOne
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Themeone Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_ThemeOne
 * @author      Magestore Developer
 */
class Simi_Themeone_Block_Adminhtml_Spot_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Simi_ThemeOne_Block_Adminhtml_Themeone_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $image_type='spotproduct';
        $image_type_id=$this->getRequest()->getParam('id');
        
        if (Mage::getSingleton('adminhtml/session')->getThemeOneData()) {
            $data = Mage::getSingleton('adminhtml/session')->getThemeOneData();
            Mage::getSingleton('adminhtml/session')->setThemeOneData(null);
        } elseif (Mage::registry('spot_data')) {
            $data = Mage::registry('spot_data')->getData();
        }
        $fieldset = $form->addFieldset('themeone_form', array(
            'legend'=>Mage::helper('themeone')->__('Spot Product information')
        ));
        $Config=Mage::getModel('themeone/config')->toOptionArray();
        $comment='<p><small>(Items '.$Config[$image_type_id-1]['label'].')</small></p>';
        $fieldset->addField('spotproduct_name', 'text', array(
            'label'        => Mage::helper('themeone')->__('Name'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'spotproduct_name',
            'after_element_html'=>$comment
        ));
         $fieldset->addField('position', 'text', array(
            'label'        => Mage::helper('themeone')->__('Position'),
            'class'        => 'required-entry validate-not-negative-number',
            'required'    => true,
            'name'        => 'position',
			'note'			=> Mage::helper('themeone')->__('This is where you want to place this these spot products in app\'s homepage. It will be counted from left to right'),
        ));
       $fieldset->addField('pagesize', 'text', array(
            'label'        => Mage::helper('themeone')->__('Max product loaded'),
            'class'        => 'required-entry validate-not-negative-number',
            'required'    => true,
            'name'        => 'pagesize',
        ));
        
        $fieldset->addField('status', 'select', array(
            'label'        => Mage::helper('themeone')->__('Status'),
            'name'        => 'status',
            'values'    => Mage::getSingleton('themeone/status')->getOptionHash(),
        ));
        
        $blockrender=$this->getLayout()->createBlock('themeone/adminhtml_grid_renderer_spot_storeimage');
        $blockrender->setType($image_type,$image_type_id,"phone");
        $fieldset->addField('image_phone_id', 'text', array(
            'label' => Mage::helper('connector')->__('Images for mobile phone'),
            'name' => 'images_phone_id',
            'value' => Mage::helper('themeone')->getDataImage($this->getRequest()->getParam('id')),
        ))->setRenderer($blockrender);

        $blockrender_tablet=$this->getLayout()->createBlock('themeone/adminhtml_grid_renderer_spot_storeimage');
        $blockrender_tablet->setType($image_type,$image_type_id,"tablet");
        $fieldset->addField('image_tablet_id', 'text', array(
            'label' => Mage::helper('connector')->__('Images for tablet'),
            'name' => 'images_tablet_id',
            'value' => Mage::helper('themeone')->getDataImage($this->getRequest()->getParam('id')),
        ))->setRenderer($blockrender_tablet);
        $form->setValues($data);
        return parent::_prepareForm();
    }
}