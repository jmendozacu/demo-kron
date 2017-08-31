<?php
$installer = $this;
 
$installer->startSetup();

$installer->run("

	-- DROP TABLE IF EXISTS {$this->getTable('pricecompare_competitor_logo')};
	
	CREATE TABLE {$this->getTable('pricecompare_competitor_logo')} (
		`id` int(10) NOT NULL,
		`product_url_attribute` varchar(225) NOT NULL,
		`logo_file_path` varchar(225) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
	
	ALTER TABLE `pricecompare_competitor_logo` ADD `website_name` VARCHAR(225) NOT NULL ;

");

$installer->endSetup();

