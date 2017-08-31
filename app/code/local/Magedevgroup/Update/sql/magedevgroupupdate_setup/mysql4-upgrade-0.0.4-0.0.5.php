<?php
/**
 * @package    Magedevgroup_Update
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->removeAttribute('catalog_category', 'sw_product_staticblock_tab_1');
$installer->removeAttribute('catalog_category', 'sw_product_staticblock_tab_2');
$installer->removeAttribute('catalog_category', 'sw_product_staticblock_tab_3');
$installer->removeAttribute('catalog_category', 'sw_product_staticblock_tab_4');
$installer->removeAttribute('catalog_category', 'sw_product_staticblock_tab_5');
$installer->removeAttribute('catalog_category', 'sw_product_attribute_tab_1');
$installer->removeAttribute('catalog_category', 'sw_product_attribute_tab_2');
$installer->removeAttribute('catalog_category', 'sw_product_attribute_tab_3');
$installer->removeAttribute('catalog_category', 'sw_product_attribute_tab_4');
$installer->removeAttribute('catalog_category', 'sw_product_attribute_tab_5');
$installer->removeAttribute('catalog_category', 'sw_product_staticblock_tab_6');
$installer->removeAttribute('catalog_category', 'sw_product_staticblock_tab_7');
$installer->removeAttribute('catalog_category', 'sw_product_staticblock_tab_8');
$installer->removeAttribute('catalog_category', 'sw_product_staticblock_tab_9');

$installer->endSetup();
