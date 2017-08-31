<?php
/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');

$installer->startSetup();

$installer->updateAttribute('catalog_product', 'fb_product', 'used_in_product_listing', 1);

$installer->endSetup();
