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
 * Themeone Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_ThemeOne
 * @author      Magestore Developer
 */
class Simi_Themeone_Block_Adminhtml_Categories_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'themeone';
        $this->_controller = 'adminhtml_categories'; // set controllers

        $this->_updateButton('save', 'label', Mage::helper('themeone')->__('Save Category'));
        $this->_updateButton('delete', 'label', Mage::helper('themeone')->__('Delete Item'));
        $this->removeButton('delete');
        $this->removeButton('reset');
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('themeone_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'themeone_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'themeone_content');
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('categories_data')
                && Mage::registry('categories_data')->getId()
        ) {
            return Mage::helper('themeone')->__("Edit '%s'", $this->htmlEscape(Mage::registry('categories_data')->getName())
            );
        }
        return Mage::helper('themeone')->__('Add Category');
    }

}