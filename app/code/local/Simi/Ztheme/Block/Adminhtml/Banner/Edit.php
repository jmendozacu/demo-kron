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
 * Ztheme Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Ztheme
 * @author      Magestore Developer
 */
class Simi_Ztheme_Block_Adminhtml_Banner_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'banner_id';
        $this->_blockGroup = 'ztheme';
        $this->_controller = 'adminhtml_banner';
        
        $this->_updateButton('save', 'label', Mage::helper('ztheme')->__('Save Banner'));
        $this->_updateButton('delete', 'label', Mage::helper('ztheme')->__('Delete Banner'));
        
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('ztheme_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'ztheme_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'ztheme_content');
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
    public function getHeaderText()
    {
        if (Mage::registry('ztheme_data')
            && Mage::registry('ztheme_data')->getId()
        ) {
            return Mage::helper('ztheme')->__("Edit Banner '%s'",
                                                $this->htmlEscape(Mage::registry('ztheme_data')->getBannerTitle())
            );
        }
        return Mage::helper('ztheme')->__('Add Banner');
    }
}