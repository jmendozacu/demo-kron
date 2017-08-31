<?php
/**
 * @category    Conversionsondemand
 * @package     Conversionsondemand_Conversions360
 * @copyright   Copyright (c) 2012 Exclusive Concepts (http://www.exclusiveconcepts.com)
 *
 */
class Conversionsondemand_Conversions360_Helper_Data extends Mage_Core_Helper_Abstract
{
  /**
   * The service URL to retrieve additional information from conversionsondemand.com
   *
   * @var const string
   */
   const _COD_SERVICE_URL = 'https://www.conversionsondemand.com/codadmin2/framework/';
  /**
   * Returns all the configuration data from the
   * magento administration related to conversionsondemand.com
   *
   * @return array
   */
  public function getCodConfig()
  {
    $codConfig = array();
    $codConfig['serviceUrl'] = self::_COD_SERVICE_URL;
    $codConfig['magentoEdition'] = Mage::getStoreConfig('conversions360_options/store/magentoedition');
    $codConfig['storeIdentifier']= strip_tags(Mage::getStoreConfig('conversions360_options/store/identifier'));
    $codConfig['snippetEnabled'] = Mage::getStoreConfig('conversions360_options/store/enabled');
    return $codConfig;
  }

  /**
   *
   * Retrieve the order details related to the provided order ids
   *
   * @param array $orders (array of increment id in case of multiple shipping)
   * @return array
   */
  public function getOrderSummary($orders)
  {
    $orderSummary = array('orderids'=>array(),'numitems'=>array(),'subtotals'=>array(),
                    'discounts'=>array(), 'totals'=>array(),
                    'itemids'=>array(),'itemcodes'=>array(),'itemqty'=>array(),
                    'itemnames'=>array(),'itemprices'=>array()
    );

    if(count($orders)>0){
      foreach($orders as $k=>$orderId){

        if (Mage::helper('checkout')->canOnepageCheckout()) {
          $order = Mage::getModel('sales/order')->load($orderId);
        } else {
          $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
        }

        $orderSummary['orderids'][] = $order->getData('increment_id');
        $orderSummary['numitems'][] = $order->getData('total_item_count');
        $orderSummary['subtotals'][] = floatval($order->getData('subtotal'));
        $orderSummary['discounts'][] = abs($order->getData('discount_amount'));
        $orderSummary['totals'][] = $order->getData('grand_total');

        $orderedItems = $order->getAllVisibleItems();
        $orderedItemSummary = array('itemcode'=>array());
        
        if(count($orderedItems > 0)){
          foreach($orderedItems as $j=>$orderedItem){
            $orderedItemSummary['itemids'][] = $orderedItem->getData('product_id');
            $orderedItemSummary['itemcodes'][] = $orderedItem->getData('sku');
            $orderedItemSummary['itemqty'][] = intval($orderedItem->getData('qty_ordered'));
            $orderedItemSummary['itemnames'][] = rawurlencode($orderedItem->getData('name'));
            $orderedItemSummary['itemprices'][] = floatval($orderedItem->getData('price'));
          }
        }
        
        if(count($orderedItemSummary['itemids']) > 0) {
          $orderSummary['itemids'][] = implode($orderedItemSummary['itemids'],',');
        }
        if(count($orderedItemSummary['itemcodes']) > 0) {
          $orderSummary['itemcodes'][] = "'" . implode($orderedItemSummary['itemcodes'],"','") . "'";
        }
        if(count($orderedItemSummary['itemqty']) > 0) {
          $orderSummary['itemqty'][] = implode($orderedItemSummary['itemqty'],',');
        }
        if(count($orderedItemSummary['itemnames']) > 0) {
          $orderSummary['itemnames'][] = "'". implode($orderedItemSummary['itemnames'],"','") . "'";
        }
        if(count($orderedItemSummary['itemprices']) > 0) {
          $orderSummary['itemprices'][] = implode($orderedItemSummary['itemprices'],',');
        }
      }
    }
    return $orderSummary;
  }
  
  /**
   * Return the sub-total amount on the user's shopping cart.
   *
   * @return float
   */
  public function getCartSubTotal()
  {
    $totals = Mage::getSingleton('checkout/cart')->getQuote()->getTotals();
    $subTotal = $totals["subtotal"]->getValue();
    return floatval($subTotal);
  }
  
  /**
   * Return the sub-total amount on the user's shopping cart.
   *
   * @return String
   */
  public function getCartItems()
  {
    $productName = array();
    $cart = Mage::getModel('checkout/cart')->getQuote();
    foreach ($cart->getAllItems() as $item) {
    $product = $item->getProduct();
    $productName[] = $product->getUrlModel()->getUrl($product);
    }
    return implode(";;",$productName);
  }
}