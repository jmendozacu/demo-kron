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
 
class Magestore_SimiPOS_Block_Adminhtml_System_Config_Forgot
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
/**
     * render config row
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id = $element->getHtmlId();
        $html  = '<tr id="row_' . $id . '">';
        $html .= '<td></td><td class="label">';
        $html .= ' <a target="_blank" href="' . Mage::getSingleton('simipos/magestore')->getForgotPasswordURL() . '">';
        $html .= Mage::helper('simipos')->__('Forgot Password') . '?</a>';
        $html .= '</td></tr>';
        return $html;
    }
}
