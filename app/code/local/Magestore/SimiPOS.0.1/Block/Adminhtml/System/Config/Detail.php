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
 
class Magestore_SimiPOS_Block_Adminhtml_System_Config_Detail
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
        $html .= '<td></td><td class="label" style="padding-top: 0px !important;">';
        $html .= ' <a target="_blank" href="' . Mage::getSingleton('simipos/magestore')->getDetailTermURL() . '">';
        $html .= Mage::helper('simipos')->__('View More') . '...</a>';
        $html .= '</td></tr>';
        return $html;
    }
}
