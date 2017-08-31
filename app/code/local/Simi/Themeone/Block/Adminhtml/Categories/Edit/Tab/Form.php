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
class Simi_Themeone_Block_Adminhtml_Categories_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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
        $image_type='category';
        $image_type_id=$this->getRequest()->getParam('id');
        
        if (Mage::getSingleton('adminhtml/session')->getThemeOneData()) {
            $data = Mage::getSingleton('adminhtml/session')->getThemeOneData();
            Mage::getSingleton('adminhtml/session')->setThemeOneData(null);
        } elseif (Mage::registry('categories_data')) {
            $data = Mage::registry('categories_data')->getData();
        }
        $fieldset = $form->addFieldset('themeone_form', array(
            'legend'=>Mage::helper('themeone')->__('Category information')
        ));
       
        if($image_type_id!=4)
        $fieldset->addField('category_id', 'select', array(
            'label' => Mage::helper('themeone')->__('Category'),
            'name' => 'category_id',
            'required' => true,
            'values' => Mage::getSingleton('themeone/allcategory')->toOptionArray(),
        ));
        else {
            $fieldset->addField('category_name', 'text', array(
            'label' => Mage::helper('themeone')->__('Category'),
            'name' => 'category_name',
            'required' => true,
            'disabled'=>true
        ));
        }
        
        $fieldset->addField('priority', 'text', array(
            'label'        => Mage::helper('themeone')->__('Position'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'priority',
            'disabled' => true
        ));
      
        $blockrender=$this->getLayout()->createBlock('themeone/adminhtml_grid_renderer_spot_storeimage');
        $blockrender->setType($image_type,$image_type_id,"phone");
        $fieldset->addField('image_phone_id', 'text', array(
            'label' => Mage::helper('connector')->__('Images'),
            'name' => 'images_phone_id',
            'value' => Mage::helper('themeone')->getDataImage($this->getRequest()->getParam('id')),
        ))->setRenderer($blockrender);


        $form->setValues($data);
        return parent::_prepareForm();
    }
}