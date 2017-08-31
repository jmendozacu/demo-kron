<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `mw_socialgift_salesrule` (
  `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `name` text NOT NULL COMMENT 'name',
  `description` text NOT NULL COMMENT 'description',
  `is_active` smallint(6) NOT NULL COMMENT 'is_active',
  `from_date` varchar(10) DEFAULT NULL,
  `to_date` varchar(10) DEFAULT NULL,
  `sg_countries` text NOT NULL COMMENT 'sg_countries',
  `sg_customer_group_ids` text NOT NULL COMMENT 'customer_group_ids',
  `sg_website_ids` text NOT NULL COMMENT 'website_ids',
  `gift_product_ids` text NOT NULL COMMENT 'gift_product_ids',
  `promotion_message` text NOT NULL COMMENT 'promotion_message conditions',
  `stop_rules_processing` smallint(6) NOT NULL COMMENT 'stop_rules_processing',
  `number_of_free_gift` int(11) NOT NULL DEFAULT '1' COMMENT 'number_of_free_gift',
  `discount_amount` tinytext NOT NULL COMMENT 'discount_amount',
  `simple_action` varchar(50) NOT NULL COMMENT 'simple_action',
  `times_used` int(11) NOT NULL COMMENT 'times_used',
  `uses_limit_by` varchar(50) NOT NULL,
  `uses_limit` varchar(50) NOT NULL,
  `social_sharing` smallint(6) NOT NULL DEFAULT '2',
  `google_plus` smallint(6) NOT NULL DEFAULT '1',
  `facebook_like` smallint(6) NOT NULL DEFAULT '1',
  `facebook_share` smallint(6) NOT NULL DEFAULT '1',
  `twitter_tweet` smallint(6) NOT NULL DEFAULT '1',
  `priority` int(50) unsigned NOT NULL,
  `sort_order` int(11) NOT NULL COMMENT 'sort_order',
  PRIMARY KEY (`rule_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='mw_socialgift_salesrule';

");

$installer->endSetup();