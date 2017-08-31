<?php

$installer = $this;
$installer->startSetup();

$sql = "DROP TABLE IF EXISTS {$installer->getTable('freegift/product')};
CREATE TABLE IF NOT EXISTS `{$installer->getTable('freegift/product')}` (
  `rule_product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` int(10) unsigned NOT NULL DEFAULT 0,
  `product_id` int(10) unsigned NOT NULL DEFAULT 0,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `stop_rules_processing` tinyint(1) NOT NULL DEFAULT '0',
  `discount_qty` INT UNSIGNED NOT NULL DEFAULT 0,
  `times_used` INT UNSIGNED NOT NULL DEFAULT 0,
  `customer_group_ids` text,
  `website_ids` text,
  `gift_product_ids` mediumtext NOT NULL default '',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rule_product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
";
$installer->run($sql);
$installer->endSetup();