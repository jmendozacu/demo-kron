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
class Magestore_SimiPOS_Model_Pdf_Order_Item extends Mage_Sales_Model_Order_Pdf_Items_Abstract
{
    /**
     * Retrieve item options
     *
     * @return array
     */
    public function getItemOptions() {
        $result = array();
        if ($options = $this->getOrderItem($this->getItem())->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }
    
    /**
     * Return item Sku
     *
     * @param  $item
     * @return mixed
     */
    public function getSku($item)
    {
        if ($this->getOrderItem($item)->getProductOptionByCode('simple_sku'))
            return $this->getOrderItem($item)->getProductOptionByCode('simple_sku');
        else
            return $item->getSku();
    }
    
    public function getOrderItem($item)
    {
    	if ($item instanceof Mage_Sales_Model_Order_Item) {
    		return $item;
    	}
    	return $item->getOrderItem();
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
        
        if ($this->getOrderItem($item)->getProductType() == 'bundle') {
        	// Render Bundle Item
        	$renderer = Mage::getSingleton('simipos/pdf_order_bundle');
        	$renderer->setOrder($order)
        	   ->setSource($this->getSource())
        	   ->setItem($item);
            return $renderer->draw();
        }
        
        $itemData = array();
        
        // Product Name and Options
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
        $itemData[] = $nameNoption;
        
        // Product SKU
        $itemData[] = $this->getSku($item);
        
        // Order QTY
        if ($item instanceof Mage_Sales_Model_Order_Item) {
        	$qty = $item->getQtyOrdered() * 1;
        } else {
            $qty = $item->getQty() * 1;
        }
        $itemData[] = ' ' . $qty;
        
        // Product Price & Subtotal
        $pricesData = array();
        $subtotalData = array();
        if ($prices = $this->getItemPricesForDisplay()) {
	        foreach ($prices as $priceData) {
	        	if (isset($priceData['label'])) {
	        		$pricesData[] = $priceData['label'];
	        		$subtotalData[] = $priceData['label'];
	        	}
	        	$pricesData[] = $priceData['price'];
	        	$subtotalData[] = $priceData['subtotal'];
	        }
        } else {
            $pricesData[] = $order->formatPriceTxt($item->getPrice());
            $subtotalData[] = $order->formatPriceTxt($item->getRowTotal());
        }
        
        // Product Price
        $itemData[] = $pricesData;
        
        // Tax
        $itemData[] = $order->formatPriceTxt($item->getTaxAmount());
        
        // Subtotal
        $itemData[] = $subtotalData;
        
        return $itemData;
	}
}
