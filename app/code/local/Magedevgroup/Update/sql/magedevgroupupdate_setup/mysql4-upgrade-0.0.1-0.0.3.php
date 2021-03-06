<?php
/**
 * @package    Magedevgroup_Update
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();
$installer->run("DROP TABLE IF EXISTS `m2epro_account`;
DROP TABLE IF EXISTS `m2epro_amazon_account`;
DROP TABLE IF EXISTS `m2epro_amazon_dictionary_category`;
DROP TABLE IF EXISTS `m2epro_amazon_dictionary_marketplace`;
DROP TABLE IF EXISTS `m2epro_amazon_dictionary_specific`;
DROP TABLE IF EXISTS `m2epro_amazon_item`;
DROP TABLE IF EXISTS `m2epro_amazon_listing`;
DROP TABLE IF EXISTS `m2epro_amazon_listing_other`;
DROP TABLE IF EXISTS `m2epro_amazon_listing_product`;
DROP TABLE IF EXISTS `m2epro_amazon_listing_product_variation`;
DROP TABLE IF EXISTS `m2epro_amazon_listing_product_variation_option`;
DROP TABLE IF EXISTS `m2epro_amazon_marketplace`;
DROP TABLE IF EXISTS `m2epro_amazon_order`;
DROP TABLE IF EXISTS `m2epro_amazon_order_item`;
DROP TABLE IF EXISTS `m2epro_amazon_processed_inventory`;
DROP TABLE IF EXISTS `m2epro_amazon_template_new_product`;
DROP TABLE IF EXISTS `m2epro_amazon_template_new_product_description`;
DROP TABLE IF EXISTS `m2epro_amazon_template_new_product_specific`;
DROP TABLE IF EXISTS `m2epro_amazon_template_selling_format`;
DROP TABLE IF EXISTS `m2epro_amazon_template_synchronization`;
DROP TABLE IF EXISTS `m2epro_attribute_set`;
DROP TABLE IF EXISTS `m2epro_buy_account`;
DROP TABLE IF EXISTS `m2epro_buy_dictionary_category`;
DROP TABLE IF EXISTS `m2epro_buy_item`;
DROP TABLE IF EXISTS `m2epro_buy_listing`;
DROP TABLE IF EXISTS `m2epro_buy_listing_other`;
DROP TABLE IF EXISTS `m2epro_buy_listing_product`;
DROP TABLE IF EXISTS `m2epro_buy_listing_product_variation`;
DROP TABLE IF EXISTS `m2epro_buy_listing_product_variation_option`;
DROP TABLE IF EXISTS `m2epro_buy_marketplace`;
DROP TABLE IF EXISTS `m2epro_buy_order`;
DROP TABLE IF EXISTS `m2epro_buy_order_item`;
DROP TABLE IF EXISTS `m2epro_buy_template_new_product`;
DROP TABLE IF EXISTS `m2epro_buy_template_new_product_attribute`;
DROP TABLE IF EXISTS `m2epro_buy_template_new_product_core`;
DROP TABLE IF EXISTS `m2epro_buy_template_selling_format`;
DROP TABLE IF EXISTS `m2epro_buy_template_synchronization`;
DROP TABLE IF EXISTS `m2epro_cache_config`;
DROP TABLE IF EXISTS `m2epro_config`;
DROP TABLE IF EXISTS `m2epro_ebay_account`;
DROP TABLE IF EXISTS `m2epro_ebay_account_policy`;
DROP TABLE IF EXISTS `m2epro_ebay_account_store_category`;
DROP TABLE IF EXISTS `m2epro_ebay_dictionary_category`;
DROP TABLE IF EXISTS `m2epro_ebay_dictionary_marketplace`;
DROP TABLE IF EXISTS `m2epro_ebay_dictionary_motor_ktype`;
DROP TABLE IF EXISTS `m2epro_ebay_dictionary_motor_specific`;
DROP TABLE IF EXISTS `m2epro_ebay_dictionary_shipping`;
DROP TABLE IF EXISTS `m2epro_ebay_dictionary_shipping_category`;
DROP TABLE IF EXISTS `m2epro_ebay_feedback`;
DROP TABLE IF EXISTS `m2epro_ebay_feedback_template`;
DROP TABLE IF EXISTS `m2epro_ebay_item`;
DROP TABLE IF EXISTS `m2epro_ebay_listing`;
DROP TABLE IF EXISTS `m2epro_ebay_listing_auto_category`;
DROP TABLE IF EXISTS `m2epro_ebay_listing_auto_category_group`;
DROP TABLE IF EXISTS `m2epro_ebay_listing_other`;
DROP TABLE IF EXISTS `m2epro_ebay_listing_product`;
DROP TABLE IF EXISTS `m2epro_ebay_listing_product_variation`;
DROP TABLE IF EXISTS `m2epro_ebay_listing_product_variation_option`;
DROP TABLE IF EXISTS `m2epro_ebay_marketplace`;
DROP TABLE IF EXISTS `m2epro_ebay_order`;
DROP TABLE IF EXISTS `m2epro_ebay_order_external_transaction`;
DROP TABLE IF EXISTS `m2epro_ebay_order_item`;
DROP TABLE IF EXISTS `m2epro_ebay_template_category`;
DROP TABLE IF EXISTS `m2epro_ebay_template_category_specific`;
DROP TABLE IF EXISTS `m2epro_ebay_template_description`;
DROP TABLE IF EXISTS `m2epro_ebay_template_other_category`;
DROP TABLE IF EXISTS `m2epro_ebay_template_payment`;
DROP TABLE IF EXISTS `m2epro_ebay_template_payment_service`;
DROP TABLE IF EXISTS `m2epro_ebay_template_policy`;
DROP TABLE IF EXISTS `m2epro_ebay_template_return`;
DROP TABLE IF EXISTS `m2epro_ebay_template_selling_format`;
DROP TABLE IF EXISTS `m2epro_ebay_template_shipping`;
DROP TABLE IF EXISTS `m2epro_ebay_template_shipping_calculated`;
DROP TABLE IF EXISTS `m2epro_ebay_template_shipping_service`;
DROP TABLE IF EXISTS `m2epro_ebay_template_synchronization`;
DROP TABLE IF EXISTS `m2epro_exceptions_filters`;
DROP TABLE IF EXISTS `m2epro_listing`;
DROP TABLE IF EXISTS `m2epro_listing_category`;
DROP TABLE IF EXISTS `m2epro_listing_log`;
DROP TABLE IF EXISTS `m2epro_listing_other`;
DROP TABLE IF EXISTS `m2epro_listing_other_log`;
DROP TABLE IF EXISTS `m2epro_listing_product`;
DROP TABLE IF EXISTS `m2epro_listing_product_variation`;
DROP TABLE IF EXISTS `m2epro_listing_product_variation_option`;
DROP TABLE IF EXISTS `m2epro_lock_item`;
DROP TABLE IF EXISTS `m2epro_locked_object`;
DROP TABLE IF EXISTS `m2epro_marketplace`;
DROP TABLE IF EXISTS `m2epro_migration_v6`;
DROP TABLE IF EXISTS `m2epro_operation_history`;
DROP TABLE IF EXISTS `m2epro_order`;
DROP TABLE IF EXISTS `m2epro_order_change`;
DROP TABLE IF EXISTS `m2epro_order_item`;
DROP TABLE IF EXISTS `m2epro_order_log`;
DROP TABLE IF EXISTS `m2epro_order_repair`;
DROP TABLE IF EXISTS `m2epro_play_account`;
DROP TABLE IF EXISTS `m2epro_play_item`;
DROP TABLE IF EXISTS `m2epro_play_listing`;
DROP TABLE IF EXISTS `m2epro_play_listing_other`;
DROP TABLE IF EXISTS `m2epro_play_listing_product`;
DROP TABLE IF EXISTS `m2epro_play_listing_product_variation`;
DROP TABLE IF EXISTS `m2epro_play_listing_product_variation_option`;
DROP TABLE IF EXISTS `m2epro_play_marketplace`;
DROP TABLE IF EXISTS `m2epro_play_order`;
DROP TABLE IF EXISTS `m2epro_play_order_item`;
DROP TABLE IF EXISTS `m2epro_play_processed_inventory`;
DROP TABLE IF EXISTS `m2epro_play_template_selling_format`;
DROP TABLE IF EXISTS `m2epro_play_template_synchronization`;
DROP TABLE IF EXISTS `m2epro_primary_config`;
DROP TABLE IF EXISTS `m2epro_processing_request`;
DROP TABLE IF EXISTS `m2epro_product_change`;
DROP TABLE IF EXISTS `m2epro_stop_queue`;
DROP TABLE IF EXISTS `m2epro_synchronization_config`;
DROP TABLE IF EXISTS `m2epro_synchronization_log`;
DROP TABLE IF EXISTS `m2epro_template_selling_format`;
DROP TABLE IF EXISTS `m2epro_template_synchronization`;
DROP TABLE IF EXISTS `m2epro_wizard`;
");
$installer->endSetup();
