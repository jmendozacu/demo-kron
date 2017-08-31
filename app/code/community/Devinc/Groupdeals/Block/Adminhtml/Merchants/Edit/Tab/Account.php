<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Devinc_Groupdeals_Block_Adminhtml_Merchants_Edit_Tab_Account extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('merchants_account', array('legend'=>Mage::helper('groupdeals')->__('Merchant Account')));

        $fieldset->addField('user_id', 'hidden',
            array(
                'name'  => 'user_id',
                'id'    => 'user_id',
            )
        );
		
        $fieldset->addField('username', 'text',
            array(
                'name'  => 'account[username]',
                'label' => Mage::helper('adminhtml')->__('User Name'),
                'id'    => 'username',
                'title' => Mage::helper('adminhtml')->__('User Name'),
                'required' => false,
            )
        );

        $data = Mage::registry('merchant_data')->getData();
        if ($data['user_id']) {
            $fieldset->addField('password', 'password',
                array(
                    'name'  => 'account[new_password]',
                    'label' => Mage::helper('adminhtml')->__('New Password'),
                    'id'    => 'new_pass',
                    'title' => Mage::helper('adminhtml')->__('New Password'),
                    'class' => 'input-text validate-password',
                    'required' => false,
                )
            );
        } else {
            $fieldset->addField('password', 'password',
                array(
                    'name'  => 'account[password]',
                    'label' => Mage::helper('adminhtml')->__('Password'),
                    'id'    => 'customer_pass',
                    'title' => Mage::helper('adminhtml')->__('Password'),
                    'class' => 'input-text validate-password',
                    'required' => false,
                )
            );
        }
        
        $fieldset->addField('confirmation', 'password',
            array(
                'name'  => 'account[password_confirmation]',
                'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
                'id'    => 'confirmation',
                'title' => Mage::helper('adminhtml')->__('Password Confirmation'),
                'class' => 'input-text validate-cpassword',
                'required' => false,
            )
        );

        $fieldset->addField('merchant_info', 'checkbox',
            array(
                'name'  => 'account[merchant_info]',
                'label' => Mage::helper('adminhtml')->__('Allow Merchant to View/Edit their info'),
                'title' => Mage::helper('adminhtml')->__('Allow Merchant to View/Edit their info'),
            )
        );		
		
        $fieldset->addField('add_edit', 'checkbox',
            array(
                'name'  => 'account[add_edit]',
                'label' => Mage::helper('adminhtml')->__('Allow Merchant to Add/Edit Deals'),
                'title' => Mage::helper('adminhtml')->__('Allow Merchant to Add/Edit Deals'),
            )
        );
		
        $fieldset->addField('approve', 'checkbox',
            array(
                'name'  => 'account[approve]',
                'label' => Mage::helper('adminhtml')->__('Require Administrator Approval'),
                'title' => Mage::helper('adminhtml')->__('Require Administrator Approval'),
                //'note'  => 'If Checked - The Administrator will have to approve each time a Deal is Added/Edited by a Merchant before changes appear in the frontend',
            )
        );
		
        $fieldset->addField('delete', 'checkbox',
            array(
                'name'  => 'account[delete]',
                'label' => Mage::helper('adminhtml')->__('Allow Merchant to Delete Deals'),
                'title' => Mage::helper('adminhtml')->__('Allow Merchant to Delete Deals'),
            )
        );
		
        $fieldset->addField('sales', 'checkbox',
            array(
                'name'  => 'account[sales]',
                'label' => Mage::helper('adminhtml')->__('Allow Merchant to View their Sales'),
                'title' => Mage::helper('adminhtml')->__('Allow Merchant to View their Sales'),
            )
        );

        $fieldset->addField('is_active', 'select',
            array(
                'name'  	=> 'account[is_active]',
                'label' 	=> Mage::helper('adminhtml')->__('This Account is'),
                'id'    	=> 'is_active',
                'title' 	=> Mage::helper('adminhtml')->__('Account Status'),
                'class' 	=> 'input-select',
                'required' 	=> false,
                'style'		=> 'width: 80px',
                'value'		=> '1',
                'values'	=> array(
                    array(
                        'label' => Mage::helper('adminhtml')->__('Inactive'),
                        'value' => '0',
                    ),
                    array(
                        'label' => Mage::helper('adminhtml')->__('Active'),
                        'value'	=> '1',
                    ),
                ),
            )
        );

       
        //set values
        if ($data) {	
        	if ($data['user_id']!='' && $data['user_id']!=0) {
				$userModel = Mage::getModel('admin/user')->load($data['user_id']);
				if ($userModel->getId()) {
					$data['username'] = $userModel->getUsername();
					$data['is_active'] = $userModel->getIsActive();
				
					$permissions = Mage::getModel('license/module')->getDecodeString($data['permissions']);
					$data['merchant_info'] = $permissions['merchant_info'];
					$data['add_edit'] = $permissions['add_edit']; 
					$data['approve'] = $permissions['approve']; 
					$data['delete'] = $permissions['delete'];
					$data['sales'] = $permissions['sales'];
				}
			}		
        	$form->setValues($data);
        }

        $this->setForm($form);
    }

}

