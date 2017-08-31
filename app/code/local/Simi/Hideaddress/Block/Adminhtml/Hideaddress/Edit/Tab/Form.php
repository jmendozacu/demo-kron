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
 * @package     Magestore_Hideaddress
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Hideaddress Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Hideaddress
 * @author      Magestore Developer
 */
class Simi_Hideaddress_Block_Adminhtml_Hideaddress_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Simi_Hideaddress_Block_Adminhtml_Hideaddress_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getHideaddressData()) {
            $data = Mage::getSingleton('adminhtml/session')->getHideaddressData();
            Mage::getSingleton('adminhtml/session')->setHideaddressData(null);
        } elseif (Mage::registry('hideaddress_data')) {
            $data = Mage::registry('hideaddress_data')->getData();
        }
        $fieldset = $form->addFieldset('hideaddress_form', array(
            'legend'=>Mage::helper('hideaddress')->__('Item information')
        ));

        $fieldset->addField('title', 'text', array(
            'label'        => Mage::helper('hideaddress')->__('Title'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'title',
        ));

        $fieldset->addField('filename', 'file', array(
            'label'        => Mage::helper('hideaddress')->__('File'),
            'required'    => false,
            'name'        => 'filename',
        ));

        $fieldset->addField('status', 'select', array(
            'label'        => Mage::helper('hideaddress')->__('Status'),
            'name'        => 'status',
            'values'    => Mage::getSingleton('hideaddress/status')->getOptionHash(),
        ));

        $fieldset->addField('content', 'editor', array(
            'name'        => 'content',
            'label'        => Mage::helper('hideaddress')->__('Content'),
            'title'        => Mage::helper('hideaddress')->__('Content'),
            'style'        => 'width:700px; height:500px;',
            'wysiwyg'    => false,
            'required'    => true,
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }
}