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
 * @package     Magestore_Themeone
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Themeone Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Themeone
 * @author      Magestore Developer
 */
class Simi_Themeone_Block_Adminhtml_Themeone_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Simi_Themeone_Block_Adminhtml_Themeone_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getThemeoneData()) {
            $data = Mage::getSingleton('adminhtml/session')->getThemeoneData();
            Mage::getSingleton('adminhtml/session')->setThemeoneData(null);
        } elseif (Mage::registry('themeone_data')) {
            $data = Mage::registry('themeone_data')->getData();
        }
        $fieldset = $form->addFieldset('themeone_form', array(
            'legend'=>Mage::helper('themeone')->__('Item information')
        ));

        $fieldset->addField('title', 'text', array(
            'label'        => Mage::helper('themeone')->__('Title'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'title',
        ));

        $fieldset->addField('filename', 'file', array(
            'label'        => Mage::helper('themeone')->__('File'),
            'required'    => false,
            'name'        => 'filename',
        ));

        $fieldset->addField('status', 'select', array(
            'label'        => Mage::helper('themeone')->__('Status'),
            'name'        => 'status',
            'values'    => Mage::getSingleton('themeone/status')->getOptionHash(),
        ));

        $fieldset->addField('content', 'editor', array(
            'name'        => 'content',
            'label'        => Mage::helper('themeone')->__('Content'),
            'title'        => Mage::helper('themeone')->__('Content'),
            'style'        => 'width:700px; height:500px;',
            'wysiwyg'    => false,
            'required'    => true,
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }
}