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
 * Simipos Location Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Location extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('simipos/location');
    }
    
    public function getOptionArray()
    {
    	$options = array();
    	foreach ($this->getCollection() as $location) {
    		$options[$location->getId()] = $location->getName();
    	}
    	return $options;
    }
    
    public function getOptionHash()
    {
    	$options = array();
        foreach ($this->getCollection() as $location) {
            $options[] = array(
                'value' => $location->getId(),
                'label' => $location->getName(),
            );
        }
        return $options;
    }
}
