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
 * SimiPOS Rewrite Quote Discount Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Quote_Discount extends Mage_SalesRule_Model_Quote_Discount
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $quote = $address->getQuote();
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }
        if ($quote->getSimiDiscountAmount() < 0.0001
            && $quote->getSimiDiscountPercent() < 0.0001
        ) {
            return $this;
        }
        
        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }
        
        if ($quote->getSimiDiscountAmount() < 0.0001) {
            // Percent discount
            foreach ($items as $item) {
                if ($item->getParentItemId()) continue;
                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    foreach ($item->getChildren() as $child) {
                        $discount = $child->getQty() * $child->getPrice() * $quote->getSimiDiscountPercent() / 100;
                        $discount = min($discount, $child->getQty() * $child->getPrice() - $child->getDiscountAmount());

                        $discount = $quote->getStore()->roundPrice($discount);
                        $baseDiscount = $discount / $quote->getStore()->convertPrice(1);

                        $child->setDiscountAmount($child->getDiscountAmount() + $discount)
                            ->setBaseDiscountAmount($child->getBaseDiscountAmount() + $baseDiscount);

                        $this->_addAmount(-$discount);
                        $this->_addBaseAmount(-$baseDiscount);
                    }
                } else {
                    $discount = $item->getQty() * $item->getPrice() * $quote->getSimiDiscountPercent() / 100;
                    $discount = min($discount, $item->getQty() * $item->getPrice() - $item->getDiscountAmount());
                    
                    $discount = $quote->getStore()->roundPrice($discount);
                    $baseDiscount = $discount / $quote->getStore()->convertPrice(1);
                    
                    $item->setDiscountAmount($item->getDiscountAmount() + $discount)
                        ->setBaseDiscountAmount($item->getBaseDiscountAmount() + $baseDiscount);
                    
                    $this->_addAmount(-$discount);
                    $this->_addBaseAmount(-$baseDiscount);
                }
            }
            if ($address->getShippingAmount()) {
                $discount = $address->getShippingAmount() * $quote->getSimiDiscountPercent() / 100;
                $discount = min($discount, $address->getShippingAmount() - $address->getShippingDiscountAmount());
                
                $discount = $quote->getStore()->roundPrice($discount);
                $baseDiscount = $discount / $quote->getStore()->convertPrice(1);
                
                $address->setShippingDiscountAmount($address->getShippingDiscountAmount() + $discount)
                    ->setBaseShippingDiscountAmount($address->getBaseShippingDiscountAmount() + $baseDiscount);
                
                $this->_addAmount(-$discount);
                $this->_addBaseAmount(-$baseDiscount);
            }
            $this->_addCustomDiscountDescription($address);
            return $this;
        }
        
        // Calculate items total
        $itemsPrice = 0;
        foreach ($items as $item) {
            if ($item->getParentItemId()) continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $itemsPrice += $item->getQty() * ($child->getQty() * $child->getPrice() - $child->getDiscountAmount());
                }
            } else {
                $itemsPrice += $item->getQty() * $item->getPrice() - $item->getDiscountAmount();
            }
        }
        $itemsPrice += $address->getShippingAmount() - $address->getShippingDiscountAmount();
        if ($itemsPrice < 0.0001) {
            return $this;
        }
        
        // Calculate custom discount for each item
        $rate = $quote->getSimiDiscountAmount() / $itemsPrice;
        if ($rate > 1) $rate = 1;
        foreach ($items as $item) {
            if ($item->getParentItemId()) continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $discount = $rate * ($child->getQty() * $child->getPrice() - $child->getDiscountAmount());
                    
                    $discount = $quote->getStore()->roundPrice($discount);
                    $baseDiscount = $discount / $quote->getStore()->convertPrice(1);
                    
                    $child->setDiscountAmount($child->getDiscountAmount() + $discount)
                        ->setBaseDiscountAmount($child->getBaseDiscountAmount() + $baseDiscount);

                    $this->_addAmount(-$discount);
                    $this->_addBaseAmount(-$baseDiscount);
                }
            } else {
                $discount = $rate * ($item->getQty() * $item->getPrice() - $item->getDiscountAmount());
                
                $discount = $quote->getStore()->roundPrice($discount);
                $baseDiscount = $discount / $quote->getStore()->convertPrice(1);
                
                $item->setDiscountAmount($item->getDiscountAmount() + $discount)
                    ->setBaseDiscountAmount($item->getBaseDiscountAmount() + $baseDiscount);
                
                $this->_addAmount(-$discount);
                $this->_addBaseAmount(-$baseDiscount);
            }
        }
        if ($address->getShippingAmount()) {
            $discount = $rate * ($address->getShippingAmount() - $address->getShippingDiscountAmount());
            
            $discount = $quote->getStore()->roundPrice($discount);
            $baseDiscount = $discount / $quote->getStore()->convertPrice(1);
            
            $address->setShippingDiscountAmount($address->getShippingDiscountAmount() + $discount)
                ->setBaseShippingDiscountAmount($address->getBaseShippingDiscountAmount() + $baseDiscount);
            
            $this->_addAmount(-$discount);
            $this->_addBaseAmount(-$baseDiscount);
        }
        $this->_addCustomDiscountDescription($address);
        return $this;
    }
    
    protected function _addCustomDiscountDescription($address)
    {
        $description = $address->getDiscountDescriptionArray();
        
        $label = $address->getQuote()->getSimiDiscountDesc();
        if (!$label) {
            $label = Mage::helper('simipos')->__('Custom Discount');
        }
        $description[0] = $label;
        
        $address->setDiscountDescriptionArray($description);
        $this->_calculator->prepareDescription($address);
    }
}
