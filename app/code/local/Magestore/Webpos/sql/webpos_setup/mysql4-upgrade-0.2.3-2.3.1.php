<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$webposHelper = Mage::helper("webpos");
if (!$webposHelper->columnExist($installer->getTable('sales_flat_order'), 'webpos_discount_amount')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales_flat_order')} ADD COLUMN `webpos_discount_amount` DECIMAL(12,4) default NULL");
}
if (!$webposHelper->columnExist($installer->getTable('sales_flat_order'), 'webpos_giftwrap_amount')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales_flat_order')} ADD COLUMN `webpos_giftwrap_amount` DECIMAL( 12, 4 ) default NULL");
}
if (!$webposHelper->columnExist($installer->getTable('sales_flat_order'), 'webpos_admin_id')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales_flat_order')} ADD COLUMN `webpos_admin_id` int(10) default NULL");
}
if (!$webposHelper->columnExist($installer->getTable('sales_flat_order'), 'webpos_admin_name')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales_flat_order')} ADD COLUMN `webpos_admin_name` varchar(255) default NULL");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_user'), 'store_ids')) {
    $installer->run("ALTER TABLE {$installer->getTable('webpos_user')} ADD `store_ids` VARCHAR(255) NOT NULL AFTER `user_id`;");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_transaction'), 'user_id')) {
    $installer->run("ALTER TABLE {$installer->getTable('webpos_transaction')} ADD `user_id` int(4) unsigned DEFAULT NULL;");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_transaction'), 'till_id')) {
    $installer->run("ALTER TABLE {$installer->getTable('webpos_transaction')} ADD `till_id` unsigned NULL DEFAULT 0;");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_transaction'), 'location_id')) {
    $installer->run("ALTER TABLE {$installer->getTable('webpos_transaction')} ADD `location_id` unsigned NULL DEFAULT 0;");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'till_id')) {
    $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} ADD `till_id` int(11) unsigned NOT NULL DEFAULT 0; ");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'location_id')) {
    $installer->run(" ALTER TABLE {$installer->getTable('sales/order')} ADD `location_id` int(11) unsigned NOT NULL DEFAULT 0; ");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_xreport'), 'till_id')) {
    $installer->run(" ALTER TABLE {$installer->getTable('webpos_xreport')} ADD `till_id` int(11) unsigned NOT NULL DEFAULT 0; ");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_xreport'), 'location_id')) {
    $installer->run(" ALTER TABLE {$installer->getTable('webpos_xreport')} ADD `location_id` int(11) unsigned NOT NULL DEFAULT 0; ");
}
if (!$webposHelper->columnExist($installer->getTable('sales/quote_payment'), 'ccforpos_ref_no')) {
    $installer->run(" ALTER TABLE {$installer->getTable('sales/quote_payment')} ADD `ccforpos_ref_no` VARCHAR( 255 ) NOT NULL; ");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order_payment'), 'ccforpos_ref_no')) {
    $installer->run(" ALTER TABLE {$installer->getTable('sales/order_payment')} ADD `ccforpos_ref_no` VARCHAR( 255 ) NOT NULL; ");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_order'), 'till_id')) {
    $installer->run(" ALTER TABLE {$installer->getTable('webpos_order')} ADD `till_id` int(11) unsigned NOT NULL DEFAULT 0; ");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_order'), 'location_id')) {
    $installer->run(" ALTER TABLE {$installer->getTable('webpos_order')} ADD `location_id` int(11) unsigned NOT NULL DEFAULT 0; ");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_cash')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/order')} ADD COLUMN `webpos_cash` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_base_cash')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/order')} ADD COLUMN `webpos_base_cash` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/invoice'), 'webpos_cash')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/invoice')} ADD COLUMN `webpos_cash` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/invoice'), 'webpos_base_cash')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/invoice')} ADD COLUMN `webpos_base_cash` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_ccforpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/order')} ADD COLUMN `webpos_ccforpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_base_ccforpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/order')} ADD COLUMN `webpos_base_ccforpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/invoice'), 'webpos_ccforpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/invoice')} ADD COLUMN `webpos_ccforpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/invoice'), 'webpos_base_ccforpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/invoice')} ADD COLUMN `webpos_base_ccforpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_cp1forpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/order')} ADD COLUMN `webpos_cp1forpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_base_cp1forpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/order')} ADD COLUMN `webpos_base_cp1forpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/invoice'), 'webpos_cp1forpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/invoice')} ADD COLUMN `webpos_cp1forpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/invoice'), 'webpos_base_cp1forpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/invoice')} ADD COLUMN `webpos_base_cp1forpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_cp2forpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/order')} ADD COLUMN `webpos_cp2forpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_base_cp2forpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/order')} ADD COLUMN `webpos_base_cp2forpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/invoice'), 'webpos_cp2forpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/invoice')} ADD COLUMN `webpos_cp2forpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/invoice'), 'webpos_base_cp2forpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/invoice')} ADD COLUMN `webpos_base_cp2forpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_change')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/order')} ADD COLUMN `webpos_change` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_base_change')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/order')} ADD COLUMN `webpos_base_change` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/invoice'), 'webpos_change')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/invoice')} ADD COLUMN `webpos_change` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/invoice'), 'webpos_base_change')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/invoice')} ADD COLUMN `webpos_base_change` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_codforpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/order')} ADD COLUMN `webpos_codforpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_base_codforpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/order')} ADD COLUMN `webpos_base_codforpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/invoice'), 'webpos_codforpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/invoice')} ADD COLUMN `webpos_codforpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/invoice'), 'webpos_base_codforpos')) {
    $installer->run("ALTER TABLE {$installer->getTable('sales/invoice')} ADD COLUMN `webpos_base_codforpos` decimal(12,4) NOT NULL default '0'");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_xreport'), 'cod_system')) {
    $installer->run("ALTER TABLE {$installer->getTable('webpos_xreport')} ADD COLUMN `cod_system` float DEFAULT '0'");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_xreport'), 'cod_count')) {
    $installer->run("ALTER TABLE {$installer->getTable('webpos_xreport')} ADD COLUMN `cod_count` float DEFAULT '0'");
}

if (!$webposHelper->columnExist($installer->getTable('webpos_xreport'), 'cp1_system')) {
    $installer->run("ALTER TABLE {$installer->getTable('webpos_xreport')} ADD COLUMN `cp1_system` float DEFAULT '0'");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_xreport'), 'cp1_count')) {
    $installer->run("ALTER TABLE {$installer->getTable('webpos_xreport')} ADD COLUMN `cp1_count` float DEFAULT '0'");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_xreport'), 'cp2_system')) {
    $installer->run("ALTER TABLE {$installer->getTable('webpos_xreport')} ADD COLUMN `cp2_system` float DEFAULT '0'");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_xreport'), 'cp2_count')) {
    $installer->run("ALTER TABLE {$installer->getTable('webpos_xreport')} ADD COLUMN `cp2_count` float DEFAULT '0'");
}
if (!$webposHelper->columnExist($installer->getTable('sales/quote_payment'), 'cp1forpos_ref_no')) {
    $installer->run(" ALTER TABLE {$installer->getTable('sales/quote_payment')} ADD `cp1forpos_ref_no` VARCHAR( 255 ) NOT NULL; ");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order_payment'), 'cp1forpos_ref_no')) {
    $installer->run(" ALTER TABLE {$installer->getTable('sales/order_payment')} ADD `cp1forpos_ref_no` VARCHAR( 255 ) NOT NULL; ");
}
if (!$webposHelper->columnExist($installer->getTable('sales/quote_payment'), 'cp2forpos_ref_no')) {
    $installer->run(" ALTER TABLE {$installer->getTable('sales/quote_payment')} ADD `cp2forpos_ref_no` VARCHAR( 255 ) NOT NULL; ");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order_payment'), 'cp2forpos_ref_no')) {
    $installer->run(" ALTER TABLE {$installer->getTable('sales/order_payment')} ADD `cp2forpos_ref_no` VARCHAR( 255 ) NOT NULL; ");
}
if (!$webposHelper->columnExist($installer->getTable('sales/quote_payment'), 'codforpos_ref_no')) {
    $installer->run(" ALTER TABLE {$installer->getTable('sales/quote_payment')} ADD `codforpos_ref_no` VARCHAR( 255 ) NOT NULL; ");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order_payment'), 'codforpos_ref_no')) {
    $installer->run(" ALTER TABLE {$installer->getTable('sales/order_payment')} ADD `codforpos_ref_no` VARCHAR( 255 ) NOT NULL; ");
}
if (!$webposHelper->columnExist($installer->getTable('sales/quote_payment'), 'cashforpos_ref_no')) {
    $installer->run(" ALTER TABLE {$installer->getTable('sales/quote_payment')} ADD `cashforpos_ref_no` VARCHAR( 255 ) NOT NULL; ");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order_payment'), 'cashforpos_ref_no')) {
    $installer->run(" ALTER TABLE {$installer->getTable('sales/order_payment')} ADD `cashforpos_ref_no` VARCHAR( 255 ) NOT NULL; ");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_user'), 'customer_group')) {
    $installer->run("ALTER TABLE {$installer->getTable('webpos_user')} ADD `customer_group`  VARCHAR( 255 )  default 'all';");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_role'), 'maximum_discount_percent')) {
    $installer->run("ALTER TABLE {$installer->getTable('webpos_role')} ADD `maximum_discount_percent`  VARCHAR( 255 )  default '';");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_user'), 'monthly_target')) {
    $installer->run("ALTER TABLE {$installer->getTable('webpos_user')} ADD `monthly_target` text;");
}
if (!$webposHelper->columnExist($installer->getTable('webpos_user'), 'seller_id')) {
    $installer->run("ALTER TABLE {$installer->getTable('webpos_user')} ADD `seller_id` int NULL;");
}
if (!$webposHelper->columnExist($installer->getTable('sales/order'), 'webpos_delivery_date')) {
	$installer->run(" ALTER TABLE {$installer->getTable('sales/order')} ADD `webpos_delivery_date` text; ");
}
$installer->endSetup();
$webposHelper->addDefaultData();
$installer = new Mage_Eav_Model_Entity_Setup ('catalog_setup');
$installer->startSetup();
$fieldList = array(
    'tax_class_id'
);
foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, $field, 'apply_to'));
    if (!in_array('customsale', $applyTo)) {
        $applyTo[] = 'customsale';
        $installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, $field, 'apply_to', implode(',', $applyTo));
    }
}
$installer->endSetup();

$product = Mage::getModel('catalog/product');
$product->getIdBySku('webpos-customsale');
if ($product->getId()) {
    try {
        $product->setData('tax_class_id', 0);
        $product->save();
    } catch (Exception $e) {
        Zend_debug::dump($e->getMessage());
    }
}

$installer->endSetup();

