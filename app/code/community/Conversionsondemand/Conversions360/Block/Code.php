<?php
/**
 * @category    Conversionsondemand
 * @package     Conversionsondemand_Conversions360
 * @copyright   Copyright (c) 2012 Exclusive Concepts (http://www.exclusiveconcepts.com)
 */

class Conversionsondemand_Conversions360_Block_Code extends Mage_Checkout_Block_Cart_Abstract
{

  public $codConfig;

  public function __construct()
  {
    $this->codConfig = Mage::helper('conversionsondemand_conversions360')->getCodConfig();
  }
  /**
   * Retrieve the store identifier of the current magento store.
   *
   * @return string
   */
  public function getStoreIdentifier()
  {
    return trim($this->codConfig['storeIdentifier']);
  }
  /**
   * Return the conversionsondemand.com service URL
   *
   * @return string
   */
  public function getCodServiceUrl()
  {
    return trim($this->codConfig['serviceUrl']);
  }
  /**
   * Check if the COD code snippet is enabled on the backend.
   *
   * @return boolean
   */
  public function codSnippetEnabled()
  {
    $codeEnabled = intval($this->codConfig['snippetEnabled']);
    return ($codeEnabled === 1) ? true: false;
  }

  /**
   * Return the sub-total amount on the user's shopping cart.
   *
   * @return float
   */
  /*public function getCartSubTotal()
  {
    $totals = Mage::getSingleton('checkout/cart')->getQuote()->getTotals();
    $subTotal = $totals["subtotal"]->getValue();
    return floatval($subTotal);
  }*/

  /**
   * Retrieve the platform of the Magento store as per backend configuration.
   *
   * @return string
   */
  public function getStorePlatform()
  {
    return trim($this->codConfig['magentoEdition']);
  }
}