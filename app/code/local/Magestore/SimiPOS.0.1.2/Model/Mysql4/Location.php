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
 * Simipos Location Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Mysql4_Location extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('simipos/location', 'location_id');
    }
    
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
    	$readAdapter    = $this->_getReadAdapter();
    	// Update User Table
    	$this->_getWriteAdapter()->update(
    	    $this->getTable('simipos/user'),
    	    array('location_id' => '0'),
    	    $readAdapter->quoteInto("location_id = ?", $object->getId())
    	);
    	// Update Order Table
    	$this->_getWriteAdapter()->update(
            $this->getTable('sales/order'),
            array('location_id' => '0'),
            $readAdapter->quoteInto("location_id = ?", $object->getId())
        );
    	// Update Order Grid
    	$this->_getWriteAdapter()->update(
            $this->getTable('sales/order_grid'),
            array('location_id' => '0'),
            $readAdapter->quoteInto("location_id = ?", $object->getId())
        );
    	
    	return parent::_afterDelete($object);
    }
}
