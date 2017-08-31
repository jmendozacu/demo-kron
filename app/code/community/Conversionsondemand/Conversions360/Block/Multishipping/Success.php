<?php
/**
 * @category    Conversionsondemand
 * @package     Conversionsondemand_Conversions360
 * @copyright   Copyright (c) 2012 Exclusive Concepts (http://www.exclusiveconcepts.com)
 *
 */
class Conversionsondemand_Conversions360_Block_Multishipping_Success extends Mage_Checkout_Block_Multishipping_Success
{
  public function getOrderDetail(){
    $orders = $this->getOrderIds();
    $orderData = Mage::helper('conversionsondemand_conversions360')->getOrderSummary($orders);
    return $orderData;
  }
}