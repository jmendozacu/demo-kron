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
class ModuleMart_Brands_Helper_Brand extends Mage_Core_Helper_Abstract{
	/**
	 * get base files dir
	 * @access public
	 */
	public function getFileBaseDir(){
		return Mage::getBaseDir('media').DS.'brand'.DS.'file';
	}
	/**
	 * get base file url
	 * @access public
	 */
	public function getFileBaseUrl(){
		return Mage::getBaseUrl('media').'brand__'.'/'.'file';
	}
	/**
	 * check if breadcrumbs can be used
	 * @access public
	 */
	public function getUseBreadcrumbs(){
		return Mage::getStoreConfigFlag('brands/brand/breadcrumbs');
	}
}