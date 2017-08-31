<?php
/**
 * @package    Magedevgroup_Update
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->removeAttribute('catalog_category', 'sw_cat_block_type');
$installer->removeAttribute('catalog_category', 'sw_cat_static_width');
$installer->removeAttribute('catalog_category', 'sw_cat_block_columns');
$installer->removeAttribute('catalog_category', 'sw_cat_block_top');
$installer->removeAttribute('catalog_category', 'sw_cat_left_block_width');
$installer->removeAttribute('catalog_category', 'sw_cat_block_left');
$installer->removeAttribute('catalog_category', 'sw_cat_right_block_width');
$installer->removeAttribute('catalog_category', 'sw_cat_block_right');
$installer->removeAttribute('catalog_category', 'sw_cat_block_bottom');
$installer->removeAttribute('catalog_category', 'sw_cat_label');
$installer->removeAttribute('catalog_category', 'sw_cat_float_type');
$installer->removeAttribute('catalog_category', 'sw_icon_image');
$installer->removeAttribute('catalog_category', 'sw_font_icon');
$installer->removeAttribute('catalog_category', 'sw_cat_hide_menu_item');

$installer->endSetup();
