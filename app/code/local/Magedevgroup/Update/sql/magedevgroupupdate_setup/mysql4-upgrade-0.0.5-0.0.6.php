<?php
/**
 * @package    Magedevgroup_Update
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->removeAttribute('catalog_product', 'sku_of_product_gift');
$installer->removeAttribute('catalog_product', 'is_product_gift_enabled');
$installer->removeAttribute('catalog_product', 'product_gift_active_from');
$installer->removeAttribute('catalog_product', 'product_gift_active_to');

$installer->run("DROP TABLE IF EXISTS `giftskus`;");

$installer->endSetup();
