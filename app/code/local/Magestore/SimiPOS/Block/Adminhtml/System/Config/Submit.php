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
 
class Magestore_SimiPOS_Block_Adminhtml_System_Config_Submit
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$this->setElement($element);
		
		if ($element->getIsSelectedTerm()) {
		    $url = $this->getUrl('simiposadmin/adminhtml_magestore/logout');
		    $label = Mage::helper('simipos')->__('Disconnect');
		} else if ($element->getIsLoggedIn()) {
			$url = $this->getUrl('simiposadmin/adminhtml_magestore/store');
			$label = Mage::helper('simipos')->__('Connect');
		} else {
		    $url = $this->getUrl('simiposadmin/adminhtml_magestore/login');
		    $label = Mage::helper('simipos')->__('Connect');
		}
		
		return $this->getLayout()->createBlock('adminhtml/widget_button', 'submit_button', array(
		    'type'    => 'button',
		    'class'   => 'save',
		    'label'   => $label,
		    'onclick' => "return configForm.submit('" . $url . "');"
		))->toHtml();
	}
}
