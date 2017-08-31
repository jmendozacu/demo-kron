<?php
$installer = $this;
 
$installer->startSetup();
 
$installer->run("
CREATE TABLE {$this->getTable('rate/product_shipping_rate')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `country_id` varchar(255) NOT NULL default '',
  `weight` varchar(255) NOT NULL default '',
  `price_range` int(11) NOT NULL default '',
  `shipping_rate` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 
    ");
 
$installer->endSetup();