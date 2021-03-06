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
 * @category 	Magestore
 * @package 	Magestore_Simicontact
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

 /**
 * Simicontact Helper
 * 
 * @category 	Magestore
 * @package 	Magestore_Simicontact
 * @author  	Magestore Developer
 */
class Simi_Simicontact_Model_Config
{
	public function toOptionArray() {
        return array(
            array('value' => '1', 'label' => Mage::helper('core')->__('List')),
            array('value' => '2', 'label' => Mage::helper('core')->__('Grid'))
			);            
    }
}