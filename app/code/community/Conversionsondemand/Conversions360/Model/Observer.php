<?php
/**
 * @category    Conversionsondemand
 * @package     Conversionsondemand_Conversions360
 * @copyright   Copyright (c) 2012 Exclusive Concepts (http://www.exclusiveconcepts.com)
 *
 */
class Conversionsondemand_Conversions360_Model_Observer
{
  /**
   *
   * The querystring parameter that disables the conversionsondemand.com services on a magento store.
   * @var const string
   */
  const FLAG_COD_DISABLED = 'codDisabled';
  /**
   *
   * Disable the conversionsondemand.com code snippet and refresh the cache.
   * @param string $observer
   */
  public function checkServiceDisabledParam($observer)
  {
    $is_set = array_key_exists(self::FLAG_COD_DISABLED, $_GET);
    if($is_set){
      try {
        Mage::getConfig()->saveConfig('conversions360_options/store/enabled', 0 );
        $allTypes = Mage::app()->useCache();
        foreach($allTypes as $type => $blah) {
          Mage::app()->getCacheInstance()->cleanType($type);
        }
      } catch (Exception $e) {
      }
    }
  }
}