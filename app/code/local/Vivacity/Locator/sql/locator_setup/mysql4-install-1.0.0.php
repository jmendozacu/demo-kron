<?php
$installer = $this;
$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('locator')};
CREATE TABLE {$this->getTable('locator')} (
  `locator_id` int(11) unsigned NOT NULL auto_increment,
  `store_name` varchar(255) NOT NULL default '',
  `address` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  `zip_code` varchar(255) NOT NULL default '',
  `country` varchar(255) NOT NULL default '',
  `state` varchar(255) NOT NULL default '',
  `faxno` varchar(255) NOT NULL default '',
  `phone` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `status` varchar(255) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `custom_icon` varchar(255) NOT NULL default '',
  `position` varchar(255) NOT NULL default '',
  `store_image` varchar(255) NOT NULL default '',
  `lat` varchar(255) NOT NULL default '',
  `long` varchar(255) NOT NULL default '',
  PRIMARY KEY (`locator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();
?>
