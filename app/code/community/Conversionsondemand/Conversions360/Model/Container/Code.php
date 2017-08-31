<?php
/**
 * @category    Conversionsondemand
 * @package     Conversionsondemand_Conversions360
 * @copyright   Copyright (c) 2012 Exclusive Concepts (http://www.exclusiveconcepts.com)
 *
 */
class Conversionsondemand_Conversions360_Model_Container_Code extends Enterprise_PageCache_Model_Container_Abstract
{
  /**
   * Get customer identifier
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
    return 'CONVERSIONSONDEMAND_CONVERSIONS360CODE'
    . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier());
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
    return $block->toHtml();
  }
}