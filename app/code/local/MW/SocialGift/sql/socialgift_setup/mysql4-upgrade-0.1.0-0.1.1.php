<?php

$installer = $this;

$sql = "ALTER TABLE `{$installer->getTable('mw_socialgift/salesrule')}`
		ADD COLUMN `all_allow_countries` INT(1) NOT NULL DEFAULT '1' AFTER `sg_countries`;
		CREATE TABLE `{$installer->getTable('mw_socialgift/reports')}` (
		  `id` INT NOT NULL AUTO_INCREMENT,
		  `rule_id` INT NOT NULL,
		  `product_id` INT NOT NULL,
		  `time_created` TIMESTAMP NOT NULL,
		  PRIMARY KEY (`id`)
		);
		";
$installer->run($sql);
$installer->endSetup();