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
 * Simipos System Configuration Field Renderer
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Block_Adminhtml_System_Config_Account
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$html = $this->_getHeaderHtml($element);
		// SimiPOS Account Content
		$this->_renderAccountInfo($element, $html);
		$html .= $this->_getFooterHtml($element);
		return $html;
	}
	
	protected function _renderAccountInfo($fieldset, &$html)
	{
		$field = $fieldset->addField('simipos_account_magestore', 'label', array(
		    'label'   => Mage::helper('simipos')->__('Connect your store to SimiPOS server to use SimiPOS on your devices'),
		))->setRenderer(Mage::getBlockSingleton('simipos/adminhtml_system_config_magestore'));
		$html .= $field->toHtml();
		
		if ($this->isSelectedTerm()) {
		  // Label for Username and Term
		  $html .= $fieldset->addField('simipos_account_username', 'note', array(
              'label' => Mage::helper('simipos')->__('Email'),
		      'text'  => $this->getAccountConfig('username'),
		  ))->toHtml();
		  
		  $html .= $fieldset->addField('simipos_account_term', 'note', array(
		      'label' => Mage::helper('simipos')->__('Your Plan'),
		      'text'  => $this->getAccountConfig('term_description'),
		  ))->toHtml();
		  
		  $field = $fieldset->addField('simipos_account_detail', 'label', array(
                'name'    => 'detail'
            ))->setRenderer(Mage::getBlockSingleton('simipos/adminhtml_system_config_detail'));
            $html .= $field->toHtml();
		} else if ($this->isLoggedIn()) {
			// Label for Username
			$html .= $fieldset->addField('simipos_account_username', 'note', array(
              'label' => Mage::helper('simipos')->__('Email'),
              'text'  => $this->getAccountConfig('username'),
            ))->toHtml();
            
			// Select purchased plan
			$field = $fieldset->addField('simipos_account_term_id', 'select', array(
                'name'    => 'term_id',
                'label'   => Mage::helper('simipos')->__('Package'),
                'value'   => $this->getAccountConfig('term_id'),
                'values'  => $this->getAccountConfig('term_options') ? unserialize($this->getAccountConfig('term_options')) : array(),
                'note'    => Mage::helper('simipos')->__('Choose your SimiPOS package for this domain')
            ));
            $html .= $field->toHtml();
		} else {
			// Show Login Form
			$field = $fieldset->addField('simipos_account_username', 'text', array(
			    'name'    => 'username',
			    'label'   => Mage::helper('simipos')->__('Email'),
			    'value'   => $this->getAccountConfig('username'),
			));//->setRenderer(Mage::getBlockSingleton('adminhtml/system_config_form_field'));
			$html .= $field->toHtml();
			
			$field = $fieldset->addField('simipos_account_password', 'password', array(
			    'name'    => 'password',
			    'label'   => Mage::helper('simipos')->__('Password')
			));
			$html .= $field->toHtml();
			$field = $fieldset->addField('simipos_account_forgot', 'label', array(
			    'name'    => 'forgot'
			))->setRenderer(Mage::getBlockSingleton('simipos/adminhtml_system_config_forgot'));
			$html .= $field->toHtml();
			
			$field = $fieldset->addField('simipos_account_mode', 'select', array(
			    'name'    => 'mode',
			    'label'   => Mage::helper('simipos')->__('Test Mode'),
			    'value'   => $this->getAccountConfig('mode'),
			    'values'  => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
			    'note'    => Mage::helper('simipos')->__('Choose YES if you are using SimiPOS for development website')
			));
			$html .= $field->toHtml();
		}
		$field = $fieldset->addField('simipos_account_submit', 'text', array(
		  'is_selected_term' => $this->isSelectedTerm(),
		  'is_logged_in'     => $this->isLoggedIn()
		))->setRenderer(Mage::getBlockSingleton('simipos/adminhtml_system_config_submit'));
		$html .= $field->toHtml();
		
		if (!$this->isLoggedIn()) {
		    $field = $fieldset->addField('simipos_account_signup', 'label', array(
            ))->setRenderer(Mage::getBlockSingleton('simipos/adminhtml_system_config_magestore'));
            $html .= $field->toHtml();
		}
	}
	
	public function isSelectedTerm()
	{
		return ($this->getAccountConfig('api_key') && $this->getAccountConfig('term_id'));
	}
	
	public function isLoggedIn()
	{
		return (bool)$this->getAccountConfig('api_key');
	}
	
	protected function getAccountConfig($field)
	{
		return Mage::getStoreConfig('simipos/account/' . $field);
	}
}
