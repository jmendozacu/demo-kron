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
class Magestore_SimiPOS_Block_Adminhtml_User_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_SimiPOS_Block_Adminhtml_User_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        $model = Mage::registry('user_data');
        
        $fieldset = $form->addFieldset('simipos_form', array(
            'legend'=>Mage::helper('simipos')->__('User information')
        ));
//        
//        $fieldset->addField('username', 'text', array(
//            'label'     => Mage::helper('simipos')->__('User Name'),
//            'class'     => 'required-entry',
//            'required'  => true,
//            'name'      => 'username',
//        ));
        
        $fieldset->addField('first_name', 'text', array(
            'label'     => Mage::helper('simipos')->__('First Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'first_name',
        ));
        
        $fieldset->addField('last_name', 'text', array(
            'label'     => Mage::helper('simipos')->__('Last Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'last_name',
        ));
        
        $fieldset->addField('email', 'text', array(
            'label'     => Mage::helper('simipos')->__('Email'),
            'class'     => 'required-entry validate-email',
            'required'  => true,
            'name'      => 'email',
        ));
        
        if ($model->getId()) {
            $fieldset->addField('password', 'password', array(
                'label'     => Mage::helper('simipos')->__('New Password'),
                'class'     => 'input-text validate-admin-password',
                'name'      => 'new_password',
            ));

            $fieldset->addField('confirmation', 'password', array(
                'label'     => Mage::helper('simipos')->__('Password Confirmation'),
                'class'     => 'input-text validate-cpassword',
                'name'      => 'password_confirmation',
            ));
        } else {
            $fieldset->addField('password', 'password', array(
                'label'     => Mage::helper('simipos')->__('New Password'),
                'class'     => 'input-text required-entry validate-admin-password',
                'name'      => 'new_password',
                'required'  => true,
            ));

            $fieldset->addField('confirmation', 'password', array(
                'label'     => Mage::helper('simipos')->__('Password Confirmation'),
                'class'     => 'input-text required-entry validate-cpassword',
                'name'      => 'password_confirmation',
                'required'  => true,
            ));
        }
        
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('stores', 'select', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('simipos')->__('Store View'),
                'title'     => Mage::helper('simipos')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        } else {
            $fieldset->addField('store_ids', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreIds(Mage::app()->getStore(true)->getId());
        }
        
//        $fieldset->addField('user_role', 'select', array(
//            'label'        => Mage::helper('simipos')->__('Select Role'),
//            'name'        => 'user_role',
//            'values'    => Mage::getSingleton('simipos/role')->getOptionHash(),
//        ));
        
        $fieldset->addField('status', 'select', array(
            'label'        => Mage::helper('simipos')->__('This account is'),
            'name'        => 'status',
            'values'    => Mage::getSingleton('simipos/status')->getOptionHash(),
        ));

        $data = $model->getData();
        unset($data['password']);
        $form->setValues($data);
        return parent::_prepareForm();
    }
}