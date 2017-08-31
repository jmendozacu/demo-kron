<?php

$installer = $this;
$sql = "ALTER TABLE `{$installer->getTable('freegift/rule')}` 
		ADD `discount_qty` INT UNSIGNED NOT NULL AFTER `is_active` ,
		ADD `times_used` INT UNSIGNED NOT NULL AFTER `discount_qty`;
		ALTER TABLE `{$installer->getTable('freegift/salesrule')}` 
		ADD `discount_qty` INT UNSIGNED NOT NULL AFTER `is_active` ,
		ADD `times_used` INT UNSIGNED NOT NULL AFTER `discount_qty`;
		";
$installer->run($sql);
$installer->endSetup();