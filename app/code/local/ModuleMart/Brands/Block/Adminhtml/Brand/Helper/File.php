<?php
 /**
 * ModuleMart_Brands extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Module-Mart License
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.modulemart.com/license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to modules@modulemart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.modulemart.com for more information.
 *
 * @category   ModuleMart
 * @package    ModuleMart_Brands
 * @author-email  modules@modulemart.com
 * @copyright  Copyright 2014 © modulemart.com. All Rights Reserved
 */
class ModuleMart_Brands_Block_Adminhtml_Brand_Helper_File extends Varien_Data_Form_Element_Abstract{
	/**
	 * constructor
	 * @access public
	 * @param array $data
	 */
	public function __construct($data){
		parent::__construct($data);
		$this->setType('file');
	}
	/**
	 * get element html
	 * @access public
	 */
	public function getElementHtml(){
		$html = '';
		$this->addClass('input-file');
		$html.= parent::getElementHtml();
		if ($this->getValue()) {
			$url = $this->_getUrl();
			if( !preg_match("/^http\:\/\/|https\:\/\//", $url) ) {
				$url = Mage::helper('brands/brand')->getFileBaseUrl() . $url;
			}
			$html .= '<br /><a href="'.$url.'">'.$this->_getUrl().'</a> ';
		}
		$html.= $this->_getDeleteCheckbox();
		return $html;
	}
	/**
	 * get the delete checkbox HTML
	 * @access protected
	 */
	protected function _getDeleteCheckbox(){
		$html = '';
		if ($this->getValue()) {
			$label = Mage::helper('brands')->__('Delete File');
			$html .= '<span class="delete-image">';
			$html .= '<input type="checkbox" name="'.parent::getName().'[delete]" value="1" class="checkbox" id="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' disabled="disabled"': '').'/>';
			$html .= '<label for="'.$this->getHtmlId().'_delete"'.($this->getDisabled() ? ' class="disabled"' : '').'> '.$label.'</label>';
			$html .= $this->_getHiddenInput();
			$html .= '</span>';
		}		
		return $html;
	}
	/**
	 * get the hidden input
	 * @access protected
	 */
	protected function _getHiddenInput(){
		return '<input type="hidden" name="'.parent::getName().'[value]" value="'.$this->getValue().'" />';
	}
	/**
	 * get the file url
	 * @access protected
	 */
	protected function _getUrl(){
		return $this->getValue();
	}
	/**
	 * get the name
	 * @access public
	 */
	public function getName(){
		return $this->getData('name');
	}
}