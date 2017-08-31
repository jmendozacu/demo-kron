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
 * @category    Magestore
 * @package     Magestore_ThemeOne
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create themeone table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('themeone_categories')};
DROP TABLE IF EXISTS {$this->getTable('themeone_images')};
DROP TABLE IF EXISTS {$this->getTable('themeone_spotproduct')};
DROP TABLE IF EXISTS {$this->getTable('themeone_banner')};

CREATE TABLE {$this->getTable('themeone_banner')} (
  `banner_id` int(11) unsigned NOT NULL auto_increment,
  `banner_name` varchar(255) NULL, 
  `banner_url` varchar(255) NULL default '',
  `banner_title` varchar(255) NULL,
  `status` int(11) NULL,  
  `website_id` smallint(5) default '0',
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  CREATE TABLE {$this->getTable('themeone_categories')} (
  `position_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `priority` smallint(11) NOT NULL default '0',
  `category_id` int(11) NOT NULL default '0',
  `category_name` text NOT NULL default '',
  PRIMARY KEY (`position_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
CREATE TABLE {$this->getTable('themeone_spotproduct')} (
  `spotproduct_id` int(11) unsigned NOT NULL auto_increment,
  `spotproduct_name` varchar(255) NOT NULL default '',
  `spotproduct_key` varchar(255) NOT NULL default '',
  `pagesize` int(11) NOT NULL default '30',
  `position` int(11) NOT NULL default '0',
  `status` smallint(11) NOT NULL default '1',
  PRIMARY KEY (`spotproduct_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE {$this->getTable('themeone_images')} (
  `image_id` int(11) unsigned NOT NULL auto_increment,
  `image_type` varchar(255) NOT NULL default '',
  `image_type_id` smallint(11) NOT NULL default '1',
  `phone_type` varchar(255) NULL default '',
  `options` smallint(11) NOT NULL default '1',
  `image_name` varchar(255) NULL default '',
  `image_delete` smallint(11) NOT NULL default '1',
  `status` smallint(11) NOT NULL default '0',
  `store_id` smallint(11) NOT NULL default '0',
 PRIMARY KEY (`image_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
Mage::helper('themeone')->addCategory($installer);
Mage::helper('themeone')->addSpotproduct($installer);

$installer->endSetup();


