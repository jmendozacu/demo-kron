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
 
class Magestore_SimiPOS_Block_Adminhtml_System_Config_Paypal
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @var string
     */
    protected $_wizardTemplate = 'simipos/paypal.phtml';

    /**
     * Set template to itself
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate($this->_wizardTemplate);
        }
        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();
        $elementHtmlId = $element->getHtmlId();
        $this->addData(array_merge(
            $this->_getButtonData($elementHtmlId, $originalData),
            $this->_getSandboxButtonData($elementHtmlId, $originalData)
        ));
        return $this->_toHtml();
    }

    /**
     * Prepare button data
     *
     * @param string $elementHtmlId
     * @param array $originalData
     * @return array
     */
    protected function _getButtonData($elementHtmlId, $originalData)
    {
        return array(
            'button_label' => Mage::helper('paypal')->__($originalData['button_label']),
            'button_url'   => $originalData['button_url'],
            'html_id' => $elementHtmlId,
        );
    }

    /**
     * Prepare sandbox button data
     *
     * @param string $elementHtmlId
     * @param array $originalData
     * @return array
     */
    protected function _getSandboxButtonData($elementHtmlId, $originalData)
    {
        return array(
            'sandbox_button_label' => Mage::helper('paypal')->__($originalData['sandbox_button_label']),
            'sandbox_button_url'   => $originalData['sandbox_button_url'],
            'sandbox_html_id' => 'sandbox_' . $elementHtmlId,
        );
    }
}
