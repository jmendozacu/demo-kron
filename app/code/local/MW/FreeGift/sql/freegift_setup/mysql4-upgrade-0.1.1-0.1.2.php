<?php

$installer = $this;

$sql = "ALTER TABLE `{$installer->getTable('freegift/salesrule')}` 
		ADD `coupon_code` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `discount_qty`;
		ALTER TABLE `{$installer->getTable('sales/quote')}` 
		ADD `freegift_coupon_code` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `coupon_code`,
		ADD `freegift_applied_rule_ids` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `applied_rule_ids`,
		ADD `freegift_ids` VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER `freegift_applied_rule_ids`;
		";
$installer->run($sql);
$installer->endSetup();