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
 * Simipos Order Print Print Data Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Pdf_Order_Bundle extends Mage_Bundle_Model_Sales_Order_Pdf_Items_Abstract
{
	public function getChilds($item)
	{
		if ($item instanceof Mage_Sales_Model_Order_Item) {
			return $item->getChildrenItems();
		}
		return parent::getChilds($item);
	}
	
    /**
     * Pdf data
     * 
     * @return array
     */
	public function draw()
	{
		$order    = $this->getOrder();
        $item     = $this->getItem();
        if ($item instanceof Mage_Sales_Model_Order_Item) {
        	$item->setOrderItem($item);
        }
        $itemsData = array();
        
        // Product Name and Options of Main Product
        $nameNoption = array();
        $nameNoption['name'] = $item->getName();
        if ($options = $this->getItemOptions()) {
        	$optionsData = array();
        	foreach ($options as $option) {
        		$optionData = array();
        		$optionData['title'] = strip_tags($option['label']);
        		if ($option['value']) {
        		    $optionData['value'] = array();
        		    if (isset($option['print_value'])) {
                        $_printValue = $option['print_value'];
                    } else {
                        $_printValue = strip_tags($option['value']);
                    }
                    $values = explode(', ', $_printValue);
                    foreach ($values as $value) {
                    	$optionData['value'][] = $value;
                    }
        		}
        		$optionsData[] = $optionData;
        	}
        	$nameNoption['options'] = $optionsData;
        }
        $mainData = array();
        $mainData[] = $nameNoption;
        
        $mainData[] = $this->getSku($item);
        if ($this->canShowPriceInfo($item)) {
            if ($item instanceof Mage_Sales_Model_Order_Item) {
                $qty = $item->getQtyOrdered() * 1;
            } else {
                $qty = $item->getQty() * 1;
            }
            // Qty
            $mainData[] = ' ' . $qty;
            
//            $pricesData = array();
//            $subtotalData = array();
//            $prices = $this->getItemPricesForDisplay();
//            foreach ($prices as $priceData) {
//                if (isset($priceData['label'])) {
//                    $pricesData[] = $priceData['label'];
//                    $subtotalData[] = $priceData['label'];
//                }
//                $pricesData[] = $priceData['price'];
//                $subtotalData[] = $priceData['subtotal'];
//            }
            // Product Price
            $mainData[] = array($order->formatPriceTxt($item->getPrice()));
            // Tax
            $mainData[] = $order->formatPriceTxt($item->getTaxAmount());
            // Subtotal
            $mainData[] = array($order->formatPriceTxt($item->getRowTotal()));
        } else {
            $mainData[] = ' ';
            $mainData[] = array();
            $mainData[] = ' ';
            $mainData[] = array();
        }
        $itemsData[] = $mainData;
        
        // Render Items for Children
        $items = $this->getChilds($item);
        $cacheData = array();
        foreach ($items as $_item) {
        	if ($_item instanceof Mage_Sales_Model_Order_Item) {
        		$_item->setOrderItem($_item);
        	}
        	if (!$_item->getOrderItem()->getParentItem()) continue;
        	$itemData = array();
            $attributes = $this->getSelectionAttributes($_item);
            if (is_array($attributes)) {
                $optionId   = $attributes['option_id'];
            } else {
                $optionId = 0;
            }
            if (!isset($cacheData[$optionId])) {
            	$mainData = array();
            	// Title
            	$nameNoption = array('options' => array());
            	$nameNoption['options'][] = array(
            	    'title' => $attributes['option_label']
            	);
            	$mainData[] = $nameNoption;
                $mainData[] = ' ';
            	$mainData[] = ' ';
                $mainData[] = array();
                $mainData[] = ' ';
                $mainData[] = array();
            	$cacheData[$optionId] = array();
            	$cacheData[$optionId][] = $mainData;
            }
            // Name and Options
            $nameNoption = array('options' => array());
            $nameNoption['options'][] = array(
                'value' => array($this->getValueHtml($_item))
            );
            $itemData[] = $nameNoption;
            $itemData[] = ' ';
            
            if ($this->canShowPriceInfo($_item)) {
            	if ($_item instanceof Mage_Sales_Model_Order_Item) {
	                $qty = $_item->getQtyOrdered() * 1;
	            } else {
	                $qty = $_item->getQty() * 1;
	            }
	            // Qty
	            $itemData[] = ' ' . $qty;
            	// Price
            	$itemData[] = array($order->formatPriceTxt($_item->getPrice()));
            	// Tax
            	$itemData[] = $order->formatPriceTxt($_item->getTaxAmount());
            	// Subtotal
            	$itemData[] = array($order->formatPriceTxt($_item->getRowTotal()));
            } else {
            	$itemData[] = ' ';
            	$itemData[] = array();
                $itemData[] = ' ';
                $itemData[] = array();
            }
        	$cacheData[$optionId][] = $itemData;
        }
        foreach ($cacheData as $itemCache) {
        	foreach ($itemCache as $itemData) {
        		$itemsData[] = $itemData;
        	}
        }
        return $itemsData;
	}
}
