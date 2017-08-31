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
 * SimiPOS Checkout API Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
abstract class Magestore_SimiPOS_Model_Api_Checkout_Abstract
    extends Magestore_SimiPOS_Model_Api_Abstract
{
    protected $_ignoredAttributeCodes = array(
        'entity_id',
        'attribute_set_id',
        'entity_type_id'
    );
    
    /**
     * Get quote for current checkout session (by API)
     * 
     * @param boolean $allowCreate
     * @return Mage_Sales_Model_Quote
     * @throws Exception
     */
    protected function _getQuote($allowCreate = false)
    {
        $quote = Mage::getModel('sales/quote');
        $quoteId = $this->_getSession()->getQuoteId();
        if ($quoteId) {
            $quote->setStoreId($this->getStoreId())
                ->load($quoteId);
        }
        if (Mage::getStoreConfig('simipos/general/ignore_checkout')) {
        	$quote->setIgnoreOldQty(true)
        	   ->setIsSuperMode(true);
        } else {
        	$quote->setIgnoreOldQty(false)
               ->setIsSuperMode(false);
        }
        if ($quote->getId()) {
            return $quote;
        }
        if (!$allowCreate) {
            throw new Exception($this->_helper->__('Quote is not found.'), 41);
        }
        // Create a new quote
        $quote->setStoreId($this->getStoreId())
            ->setIsActive(false)
            ->setIsMultiShipping(false);
        try {
            $quote->save();
            $this->_getSession()->setQuoteId($quote->getId());
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 42); // Cannot create quote
        }
        return $quote;
    }
    
    /**
     * Prepare data for API
     * 
     * @param array $data
     * @return array
     */
    protected function _prepareData($data)
    {
        foreach ($this->_ignoredAttributeCodes as $ignoreAttribute) {
            if (isset($data[$ignoreAttribute])) {
                unset($data[$ignoreAttribute]);
            }
        }
        return $data;
    }
    
    /**
     * get attribute for current object (ignore object type)
     * 
     * @param Varien_Object $object
     * @return array
     */
    protected function _getAttributes($object)
    {
        $result = array();
        if (!is_object($object)) {
            return $result;
        }
        foreach ($object->getData() as $attribute => $value) {
            if (is_object($value) || in_array($attribute, $this->_ignoredAttributeCodes)) {
                continue;
            }
            $result[$attribute] = $value;
        }
        if ($object->getId()) {
            $result['id'] = $object->getId();
        }
        return $result;
    }
    
    /**
     * Response item data
     * 
     * @param Varien_Object $item
     * @return array
     */
    protected function _getItemData($item)
    {
        $result = array();
        $allowedAttribute = array(
            'product_id', 'store_id', 'sku', 'name', 'qty', 'price', 'custom_price',
            'discount_percent', 'discount_amount',
            'product_type', 'regular_price', 'is_virtual'
        );
        foreach ($allowedAttribute as $attribute) {
            if ($item->hasData($attribute)) {
                $result[$attribute] = $item->getData($attribute);
            }
        }
        if ($item->getId()) {
            $result['id'] = $item->getId();
        }
        return $result;
    }
}
