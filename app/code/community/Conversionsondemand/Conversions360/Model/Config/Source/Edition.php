<?php
/**
 * @category    Conversionsondemand
 * @package     Conversionsondemand_Conversions360
 * @copyright   Copyright (c) 2012 Exclusive Concepts (http://www.exclusiveconcepts.com)
 *
 */

class Conversionsondemand_Conversions360_Model_Config_Source_Edition
{
  /**
   * Return the dropdown options required for the Configuration page on the Magento backend
   *
   * @return array
   */
  public function toOptionArray()
  {
    return array(
    array('value'=>'magento', 'label'=>Mage::helper('adminhtml')->__('Magento Community')),
    array('value'=>'magentoenterprise', 'label'=>Mage::helper('adminhtml')->__('Magento Enterprise'))
    );
  }
}