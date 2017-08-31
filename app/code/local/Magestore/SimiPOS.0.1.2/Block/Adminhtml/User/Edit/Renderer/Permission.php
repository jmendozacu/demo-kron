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
 * Permission Renderer Block
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Block_Adminhtml_User_Edit_Renderer_Permission
    extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface
{
	protected $_element;
	
	/**
	 * getter and setter for element
	 */
	public function setElement($element)
	{
		$this->_element = $element;
		return $this;
	}
	
	public function getElement()
	{
		return $this->_element;
	}
	
	/**
	 * construct block
	 */
	public function __construct()
	{
		$this->setTemplate('simipos/permission.phtml');
	}
	
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$this->setElement($element);
		return $this->toHtml();
	}
	
	public function getValue($key)
	{
		$value = $this->getElement()->getValue();
		if (is_array($value) && isset($value[$key])) {
			return $value[$key];
		}
		return Magestore_SimiPOS_Model_Role::PERMISSION_OWNER;
	}
}
