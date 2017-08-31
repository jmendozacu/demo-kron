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
 */
class Simi_Ztheme_Block_Adminhtml_Spot_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $image_type='spotproduct';
        $image_type_id=$this->getRequest()->getParam('id');
        /*
        if (Mage::getSingleton('adminhtml/session')->getThemeOneData()) {
            $data = Mage::getSingleton('adminhtml/session')->getThemeOneData();
            Mage::getSingleton('adminhtml/session')->setThemeOneData(null);
        } else hainh
         */
        if (Mage::registry('spot_data')) {
            $data = Mage::registry('spot_data')->getData();
        }
        $fieldset = $form->addFieldset('ztheme_form', array(
            'legend'=>Mage::helper('ztheme')->__('Spot Product information')
        ));
        $Config=Mage::getModel('ztheme/config')->toOptionArray();
        $comment='<p><small>(Items '.$Config[$image_type_id-1]['label'].')</small></p>';
        $fieldset->addField('spotproduct_name', 'text', array(
            'label'        => Mage::helper('ztheme')->__('Name'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'spotproduct_name',
            'after_element_html'=>$comment
        ));
        
         $fieldset->addField('position', 'text', array(
            'label'        => Mage::helper('ztheme')->__('Position'),
            'class'        => 'required-entry validate-not-negative-number',
            'required'    => true,
            'name'        => 'position',
			'note'			=> Mage::helper('ztheme')->__('This is where you want to place this these spot products in app\'s homepage. It will be counted from left to right'),
        ));
       $fieldset->addField('pagesize', 'text', array(
            'label'        => Mage::helper('ztheme')->__('Max product loaded'),
            'class'        => 'required-entry validate-not-negative-number',
            'required'    => true,
            'name'        => 'pagesize',
        ));
        
        $fieldset->addField('status', 'select', array(
            'label'        => Mage::helper('ztheme')->__('Status'),
            'name'        => 'status',
            'values'    => Mage::getSingleton('ztheme/status')->getOptionHash(),
        ));
        
        if (isset($data['spotproduct_banner_name']) && $data['spotproduct_banner_name']) {
            $data['spotproduct_banner_name'] = Mage::getBaseUrl('media') . 'simi/ztheme/spotbanner/' . $data['website_id'] . '/' . $data['spotproduct_banner_name'];
        }
        
        if (isset($data['spotproduct_banner_name_tablet']) && $data['spotproduct_banner_name_tablet']) {
            $data['spotproduct_banner_name_tablet'] = Mage::getBaseUrl('media') . 'simi/ztheme/spotbanner_tab/' . $data['website_id'] . '/' . $data['spotproduct_banner_name_tablet'];
        }
        
       $fieldset->addField('spotproduct_banner_name', 'image', array(
            'label' => Mage::helper('ztheme')->__('Image for Phone (width:900px, height:600px)'),
            'required' => FALSE,
            'name' => 'spotproduct_banner_name',
        ));
        
       $fieldset->addField('spotproduct_banner_name_tablet', 'image', array(
            'label' => Mage::helper('ztheme')->__('Image for Tablet (width:1800px, height:1200px)'),
            'required' => FALSE,
            'name' => 'spotproduct_banner_name_tablet',
        ));
       
        $form->setValues($data);
        return parent::_prepareForm();
    }
}