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
 * @package     Magestore_Ztheme
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Ztheme Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Ztheme
 * @author      Magestore Developer
 */
class Simi_Ztheme_Block_Adminhtml_Banner_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Simi_Ztheme_Block_Adminhtml_Ztheme_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);


        if (Mage::getSingleton('adminhtml/session')->getZthemeData()) {
            $data = Mage::getSingleton('adminhtml/session')->getZthemeData();
            Mage::getSingleton('adminhtml/session')->setZthemeData(null);
        } elseif (Mage::registry('ztheme_banner_data')) {
            $data = Mage::registry('ztheme_banner_data')->getData();
        }


        $fieldset = $form->addFieldset('ztheme_form', array(
            'legend' => Mage::helper('ztheme')->__('Banner information')
        ));

        $fieldset->addField('website_id', 'select', array(
            'label' => Mage::helper('ztheme')->__('Website'),
            'name' => 'website_id',
            'values' => Mage::getSingleton('ztheme/status')->getWebsite(),
            'disabled' => true
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('ztheme')->__('Status'),
            'name' => 'status',
            'values' => Mage::getSingleton('ztheme/status')->getOptionHash(),
        ));

        $fieldset->addField('banner_title', 'text', array(
            'label' => Mage::helper('ztheme')->__('Title'),
            'class' => 'required-entry',
            'required' => TRUE,
            'name' => 'banner_title',
        ));


        if (isset($data['banner_name']) && $data['banner_name']) {
            $data['banner_name'] = Mage::getBaseUrl('media') . 'simi/ztheme/banner/' . $data['website_id'] . '/' . $data['banner_name'];
        }
        
        if (isset($data['banner_name_tablet']) && $data['banner_name_tablet']) {
            $data['banner_name_tablet'] = Mage::getBaseUrl('media') . 'simi/ztheme/banner_tab/' . $data['website_id'] . '/' . $data['banner_name_tablet'];
        }

        $fieldset->addField('banner_name', 'image', array(
            'label' => Mage::helper('ztheme')->__('Image for Phone (width:900px, height:600px)'),
            'required' => FALSE,
            'name' => 'banner_name',
        ));
        
       $fieldset->addField('banner_name_tablet', 'image', array(
            'label' => Mage::helper('ztheme')->__('Image for Tablet (width:1800px, height:1200px)'),
            'required' => FALSE,
            'name' => 'banner_name_tablet',
        ));


        $fieldset->addField('category_id', 'select', array(
            'label' => 'Category',
            'class' => 'required-entry',
            'required' => true,
            'name' => 'category_id',
            'values' => $this->get_categories(),
            'disabled' => false,
            'readonly' => false,
            'tabindex' => 1
        ));

        
        $fieldset->addField('banner_position', 'text', array(
          'label'     => Mage::helper('ztheme')->__('Position'),
          'class'     => 'validate-number',
          'name'      => 'banner_position'));
        
        $fieldset->addField('banner_content', 'editor', array(
            'name' => 'banner_content',
            'label' => Mage::helper('ztheme')->__('Content'),
            'title' => Mage::helper('ztheme')->__('Content'),
            'style' => 'width:500px; height:150px;',
            'wysiwyg' => false,
            'required' => FALSE,
        ));
        
        



        $form->setValues($data);
        return parent::_prepareForm();
    }

    protected function get_categories() {

        $category = Mage::getModel('catalog/category');
        $tree = $category->getTreeModel();
        $tree->load();
        $ids = $tree->getCollection()->getAllIds();
        $arr = array();
        if ($ids) {
            foreach ($ids as $id) {
                $cat = Mage::getModel('catalog/category');
                $cat->load($id);
                $arr[$id] = $cat->getName();
                if (!$cat->getName())
                    $arr[$id] = '';
            }
        }
        asort($arr);
        return $arr;
    }

}
