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
 * Simipos Customer Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Customer
extends Mage_Customer_Model_Customer
{
	public function _construct()
	{
		$this->_init('simipos/customer');
	}
}
