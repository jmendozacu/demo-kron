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
 * Simipos User Collection
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Mysql4_Rule_Collection extends Mage_SalesRule_Model_Mysql4_Rule_Collection
{
	public function setValidationFilter($websiteId, $customerGroupId, $couponCode = '', $now = null)
	{
		parent::setValidationFilter($websiteId, $customerGroupId, $couponCode, $now);
		// Valid Filter for SimiPOS
		$session = Mage::getSingleton('simipos/session');
		if ($session->getSessionId()) {
			if (!Mage::getStoreConfig('simipos/checkout/allow_discount', $session->getCurrentStoreId())) {
				$this->getSelect()->where(
				    'main_table.coupon_type <> ?',
				    Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON
				);
			}
		}
		return $this;
	}
}
