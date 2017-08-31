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
 * SimiPOS Location API Model
 * Use to call api with prefix: location
 * Methods:
 *  all
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Location extends Magestore_SimiPOS_Model_Api_Abstract
{
	public function apiAll()
	{
		$result = array();
		$collection = Mage::getResourceModel('simipos/location_collection');
		$result['total'] = $collection->count() + 1;
		$result['0'] = array(
		    'name'    => Mage::helper('simipos')->__('Unlocated')
		);
		foreach ($collection as $loc) {
			$result[$loc->getId()] = array(
			    'name'   => $loc->getName(),
			);
		}
		
		return $result;
	}
}
