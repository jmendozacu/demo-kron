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
 * SimiPOS Shipping Methods Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Source_Paypalhere
{
	public function toOption()
	{
		$helper = Mage::helper('simipos');
		return array(
		  'cash'      => $helper->__('Cash'),
		  'card'      => $helper->__('Credit Card'),
		  'paypal'    => $helper->__('Paypal'),
		  'invoice'   => $helper->__('Invoice'),
		  'check'     => $helper->__('Check'),
		);
	}
	
    public function toOptionArray()
    {
        $options = array();
        foreach ($this->toOption() as $code => $title) {
        	$options[] = array(
        	    'value'    => $code,
        	    'label'    => $title
        	);
        }
        return $options;
    }
}
