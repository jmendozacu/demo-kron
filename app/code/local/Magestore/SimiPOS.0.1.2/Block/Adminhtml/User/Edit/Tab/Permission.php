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
 * Simipos User's Orders Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Block_Adminhtml_User_Edit_Tab_Permission
    extends Mage_Adminhtml_Block_Widget_Form
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
        
        $fieldset = $form->addFieldset('permission_form', array(
            'legend'=>Mage::helper('simipos')->__('User Role information')
        ));
        
        $fieldset->addField('user_role', 'select', array(
            'label'     => Mage::helper('simipos')->__('Select Role'),
            'name'      => 'user_role',
            'values'    => Mage::getSingleton('simipos/role')->getOptionHash(),
            'onchange'  => 'changeUserRole(this);'
        ));
        
        $fieldset->addField('role_permission', 'text', array(
            'label' => Mage::helper('simipos')->__('Sales Staff Permission'),
            'name'  => 'role_permission'
        ))->setRenderer($this->getLayout()->createBlock('simipos/adminhtml_user_edit_renderer_permission'));
        
        $form->setValues($model->getData());
        return parent::_prepareForm();
    }
}
