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
class ModuleMart_Brands_Block_Adminhtml_Brand extends Mage_Adminhtml_Block_Widget_Grid_Container{
	/**
	 * constructor
	 * @access public
	 */
	public function __construct(){
		$this->_controller 		= 'adminhtml_brand';
		$this->_blockGroup 		= 'brands';
		$this->_headerText 		= Mage::helper('brands')->__('Brands Management');
		$this->_addButtonLabel 	= Mage::helper('brands')->__('Add Brand');
		parent::__construct();
	}
}