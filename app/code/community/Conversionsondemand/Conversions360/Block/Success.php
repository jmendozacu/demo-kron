<?php
/**
 * @category    Conversionsondemand
 * @package     Conversionsondemand_Conversions360
 * @copyright   Copyright (c) 2012 Exclusive Concepts (http://www.exclusiveconcepts.com)
 *
 */
class Conversionsondemand_Conversions360_Block_Success extends Mage_Checkout_Block_Onepage_Success
{
  public function getOrderDetail(){
    $orders = Mage::getSingleton('checkout/session')->getLastOrderId();
    $orderData = Mage::helper('conversionsondemand_conversions360')->getOrderSummary(array($orders));
    return $orderData;
  }
}