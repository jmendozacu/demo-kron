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
 * @package     Magestore_Ztheme
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * create ztheme table
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('ztheme_banners')};
DROP TABLE IF EXISTS {$this->getTable('ztheme_spotproduct')};

CREATE TABLE {$this->getTable('ztheme_banners')} (
  `banner_id` int(11) unsigned NOT NULL auto_increment,
  `banner_title` varchar(255) NOT NULL default '',
  `banner_name` varchar(255) NOT NULL default '',
  `banner_name_tablet` varchar(255) NOT NULL default '',
  `banner_content` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `website_id` smallint(5) default '0',
  `category_id` smallint(5) default '0',
  `banner_position` smallint(5) default '0',
  
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('ztheme_spotproduct')} (
  `spotproduct_id` int(11) unsigned NOT NULL auto_increment,
  `spotproduct_name` varchar(255) NOT NULL default '',
  `spotproduct_banner_name` varchar(255) NOT NULL default '',
  `spotproduct_banner_name_tablet` varchar(255) NOT NULL default '',
  `spotproduct_key` varchar(255) NOT NULL default '',
  `pagesize` int(11) NOT NULL default '30',
  `position` int(11) NOT NULL default '0',
  `status` smallint(11) NOT NULL default '1',
  PRIMARY KEY (`spotproduct_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
Mage::helper('ztheme')->addSpotproduct($installer);

$installer->endSetup();

