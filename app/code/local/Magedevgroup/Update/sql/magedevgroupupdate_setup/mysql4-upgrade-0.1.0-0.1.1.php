<?php
/**
 * @package    Magedevgroup_Update
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->removeAttribute('catalog_product', 'xero_sales_account_code');

$installer->getConnection()->dropColumn($installer->getTable('tax_calculation_rate'), 'xero_rate');

$installer->getConnection()->dropColumn($installer->getTable('sales_flat_order_item'), 'xero_rate');

$installer->getConnection()->dropTable('foomanconnect_order');
$installer->getConnection()->dropTable('foomanconnect_invoice');
$installer->getConnection()->dropTable('foomanconnect_creditmemo');
$installer->getConnection()->dropTable('foomanconnect_item');
$installer->getConnection()->dropTable('foomanconnect_customer');
$installer->getConnection()->dropTable('foomanconnect_tracking_rule');
$installer->endSetup();
