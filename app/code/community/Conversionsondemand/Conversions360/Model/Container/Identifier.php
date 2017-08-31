<?php
/**
 * Placeholder container for the identifiers related to page type.
 *
 * @category    Conversionsondemand
 * @package     Conversionsondemand_Conversions360
 * @copyright   Copyright (c) 2012 Exclusive Concepts (http://www.exclusiveconcepts.com)
 */
class Conversionsondemand_Conversions360_Model_Container_Identifier extends Enterprise_PageCache_Model_Container_Abstract
{

  /**
   * Get customer identifier from cookies
   *
   * @return string
   */
  protected function _getIdentifier()
  {
    return md5(strtotime('now'));
  }

  /**
   * Get cache identifier
   *
   * @return string
   */
  protected function _getCacheId()
  {
    return 'CONVERSIONSONDEMAND_CONVERSIONS360_IDENTIFIER' . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier());
  }

  /**
   * Render block content
   *
   * @return string
   */
  protected function _renderBlock()
  {
    $block = $this->_placeholder->getAttribute('block');
    $template = $this->_placeholder->getAttribute('template');

    $block = new $block;
    $block->setTemplate($template);

    $block->setLayout(Mage::app()->getLayout());
    $pageIdentifier = $this->_placeholder->getAttribute('pageIdentifier');
    $cartSubTotal = $this->_placeholder->getAttribute('cartSubTotal');
    $cartItems = $this->_placeholder->getAttribute('cartItems');
    $cod_cartDetails['cartSubTotal'] = $cartSubTotal;
    $cod_cartDetails['cartItems'] = $cartItems;

    return $block->codIdentifierHtml($pageIdentifier , $cod_cartDetails);
  }
}