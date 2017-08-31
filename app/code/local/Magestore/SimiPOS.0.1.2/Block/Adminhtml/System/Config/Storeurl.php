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
class Magestore_SimiPOS_Block_Adminhtml_System_Config_Storeurl extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setData('onclick', 'this.select();');
        $element->setData('readonly', 'true');
        $element->setData('name', '');
        
        // get store API url
        $adminUrl = Mage::app()->getStore()->getBaseUrl(
            Mage_Core_Model_Store::URL_TYPE_LINK,
            Mage::getStoreConfigFlag('web/secure/use_in_adminhtml')
        );
        $element->setData('value', $adminUrl);
        
        return parent::_getElementHtml($element);
    }
}

