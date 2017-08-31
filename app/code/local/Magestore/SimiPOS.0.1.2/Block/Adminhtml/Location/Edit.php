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
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Simipos Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Block_Adminhtml_Location_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'simipos';
        $this->_controller = 'adminhtml_location';
        
        $this->_updateButton('save', 'label', Mage::helper('simipos')->__('Save Location'));
        $this->_updateButton('delete', 'label', Mage::helper('simipos')->__('Delete Location'));
        
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                if (editForm.validator.validate()) {
                    var loaderArea = $$('#html-body .wrapper')[0];
                    Element.clonePosition($('loading-mask'), $(loaderArea), {offsetLeft:-2});
                    toggleSelectsUnderBlock($('loading-mask'), false);
                    Element.show('loading-mask');
                    setLoaderPosition();
                    editForm.submit($('edit_form').action+'back/edit/');
                }
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
        if (Mage::registry('location_data')
            && Mage::registry('location_data')->getId()
        ) {
            return Mage::helper('simipos')->__("Edit location '%s'",
                $this->htmlEscape(Mage::registry('location_data')->getName())
            );
        }
        return Mage::helper('simipos')->__('Add Location');
    }
}
