<?php 

$installer = $this;
$installer->startSetup();
$installer->run("

	CREATE TABLE IF NOT EXISTS {$this->getTable('watermark/watermark')} (
		`id`  int(10) NOT NULL AUTO_INCREMENT,
		`name` varchar(255) DEFAULT NULL,
		`sku` varchar(64) DEFAULT NULL,
		`status` varchar(100) DEFAULT NULL,		
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

	
");

$installer->endSetup();
?>


