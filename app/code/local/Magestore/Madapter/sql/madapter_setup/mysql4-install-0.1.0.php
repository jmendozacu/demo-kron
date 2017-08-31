<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create madapter table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('madapter')};
DROP TABLE IF EXISTS {$this->getTable('madapter_banner')};
DROP TABLE IF EXISTS {$this->getTable('madapter_device')};

CREATE TABLE {$this->getTable('madapter')} (
  `madapter_id` int(11) unsigned NOT NULL auto_increment,
  `transaction_id` varchar(255) NULL, 
  `transaction_name` varchar(255) NULL,
  `transaction_email` varchar(255) NULL,
  `status` varchar(255) NULL,
  `amount` varchar(255) NULL,    
  `currency_code` varchar(255) NULL,  
  `transaction_dis` varchar(255) NULL,
  `order_id` int(11) NULL,  
  PRIMARY KEY (`madapter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('madapter_banner')} (
  `banner_id` int(11) unsigned NOT NULL auto_increment,
  `banner_name` varchar(255) NULL, 
  `banner_url` varchar(255) NULL,
  `banner_title` varchar(255) NULL,
  `status` int(11) NULL,
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('madapter_device')} (
  `device_id` int(11) unsigned NOT NULL auto_increment,
  `device_token` varchar(255) NOT NULL UNIQUE,   
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();

