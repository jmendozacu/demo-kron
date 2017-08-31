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
class Magestore_SimiPOS_Model_Mysql4_User_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('simipos/user');
    }
    
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof Mage_Core_Model_Store) {
                $store = $store->getId();
            }
            if ($withAdmin) {
                $this->getSelect()
                    ->where('FIND_IN_SET(?, store_ids) OR FIND_IN_SET(0, store_ids)', $store);
            } else {
                $this->addFieldToFilter('store_ids', array('finset' => $store));
            }
            $this->setFlag('store_filter_added', true);
        }
        return $this;
    }
}
