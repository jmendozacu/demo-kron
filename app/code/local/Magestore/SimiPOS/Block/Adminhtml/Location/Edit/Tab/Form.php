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
 * Simipos Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Block_Adminhtml_Location_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_SimiPOS_Block_Adminhtml_Location_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $model = Mage::registry('location_data');
        
        $fieldset = $form->addFieldset('simipos_form', array(
            'legend'=>Mage::helper('simipos')->__('Location information')
        ));
        
        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('simipos')->__('Location Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));
        
        $fieldset->addField('address', 'textarea', array(
            'label'     => Mage::helper('simipos')->__('Address'),
            'required'  => false,
            'name'      => 'address',
        ));
        
        $form->setValues($model->getData());
        return parent::_prepareForm();
    }
}