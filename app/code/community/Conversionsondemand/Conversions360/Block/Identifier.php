<?php
/**
 * @category    Conversionsondemand
 * @package     Conversionsondemand_Conversions360
 * @copyright   Copyright (c) 2012 Exclusive Concepts (http://www.exclusiveconcepts.com)
 *
 */

class Conversionsondemand_Conversions360_Block_Identifier extends Mage_Core_Block_Text
{
  /**
   *
   * The page identifier that checks the current page type.
   * @var string
   * @example PRODUCT, CART etc.
   */
  public $pageIdentifier;
  /**
   * Set the page identifier of the currently viewed page.
   * Called on the layout file conversionsondemand.xml
   *
   * @param string $identifier
   *
   */
  public function setCodIdentifier( $identifier )
  {
    $this->pageIdentifier = $identifier;
  }

  /**
   * Generate the JS codebase required for the conversionsondemand snippet to identify the current page type.
   *
   * @param string $identifier
   * @param string $cartSubTotal
   *
   * @return string
   */
  public function codIdentifierHtml ($identifier, $cod_cartDetails)
  {
    $codConfig = Mage::helper('conversionsondemand_conversions360')->getCodConfig();
    $validIdentifiers = array('PRODUCT','CART','CHECKOUT','SUCCESS');
    $codIdentifierHtml = '';

    if(strlen($codConfig['storeIdentifier']) === 0) {
      return $codIdentifierHtml;
    }

    if($codConfig['snippetEnabled'] == 0) {
      return $codIdentifierHtml;
    }

    $commentTag = '<!--- Conversions On Demand script, more info at www.conversionsondemand.com - Do Not Remove or Replace -->';
    $autoDiscounterScript = '';

    if(in_array($identifier,$validIdentifiers)){
      if($identifier == 'CART'){
        $autoDiscounterScript = '<script language=javascript>'
        .'var cod_cartSubtotalAmt = parseFloat(' . $cod_cartDetails["cartSubTotal"] . ');'
        .'var cod_cartItems = "' . $cod_cartDetails["cartItems"] . '";'
        . 'var COD_CONFIG= {"platform": "' . $codConfig['magentoEdition'] . '","stoken":"' . $codConfig['storeIdentifier'] . '"};'
        .'</script>'
        .'<script language="javascript" '
        .'src="'. $codConfig['serviceUrl'] .'core/couponHandler.php'
        .'?p='.$codConfig['magentoEdition'].'&d='.$codConfig['storeIdentifier'].'"></script>';
      }

      $codIdentifierHtml = $commentTag
      . '<script language="javascript">'
      . 'var cod_page_guid = "' . $identifier . '";'
      . '</script>'
      . $autoDiscounterScript
      . $commentTag;

    } else {
      $codIdentifierHtml = $commentTag
      . '<script language=javascript>'
      . 'var cod_page_guid = "NON-PRODUCT";'
      . '</script>'
      . $commentTag;
    }

    return $codIdentifierHtml;
  }

  /**
   * Return additional data required for Magento Full Page Caching.
   *
   * @return array
   */
  public function getCacheKeyInfo()
  {
    $cacheKeyInfo = parent::getCacheKeyInfo();
    $cacheKeyInfo['pageIdentifier'] = $this->pageIdentifier;
    $cacheKeyInfo['cartSubTotal'] = Mage::helper('conversionsondemand_conversions360')->getCartSubTotal();
    $cacheKeyInfo['cartItems'] = Mage::helper('conversionsondemand_conversions360')->getCartItems();    
        

    return $cacheKeyInfo;
  }

  /**
   * Returns the javascript code that is used to identify the currently viewed page type on the store.
   *
   * @return string
   */
  protected function _toHtml()
  {
    $cod_cartDetails = array();
    //$cartSubTotal = Mage::helper('conversionsondemand_conversions360')->getCartSubTotal();
    $cod_cartDetails['cartSubTotal'] = Mage::helper('conversionsondemand_conversions360')->getCartSubTotal();
    $cod_cartDetails['cartItems'] = Mage::helper('conversionsondemand_conversions360')->getCartItems();
    return $this->codIdentifierHtml($this->pageIdentifier, $cod_cartDetails);
  }
}