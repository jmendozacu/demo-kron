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
 * Rewrite Stock Item Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Stock_Item extends Mage_CatalogInventory_Model_Stock_Item
{
	public function getBackorders()
	{
		if (Mage::getSingleton('simipos/session')->getSessionId()
		 && Mage::getStoreConfig('simipos/general/ignore_checkout')
		) {
			return Mage_CatalogInventory_Model_Stock::BACKORDERS_YES_NOTIFY;
		}
		return parent::getBackorders();
	}
	
	public function getIsInStock()
	{
	    if (Mage::getSingleton('simipos/session')->getSessionId()
         && Mage::getStoreConfig('simipos/general/ignore_checkout')
        ) {
            return true;
        }
        return parent::getIsInStock();
	}
}
