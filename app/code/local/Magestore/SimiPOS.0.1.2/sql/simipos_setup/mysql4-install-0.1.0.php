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
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * database for simipos
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simipos_session')};
DROP TABLE IF EXISTS {$this->getTable('simipos_user')};

CREATE TABLE {$this->getTable('simipos_user')} (
    `user_id` int(10) unsigned NOT NULL auto_increment,
    `first_name` varchar(255) NULL,
    `last_name` varchar(255) NULL,
    `email` varchar(255) NULL,
    `username` varchar(255) NULL,
    `password` varchar(255) NULL,
    `status` smallint(5) NOT NULL default '0',
    `created_time` datetime NULL,
    `user_role` smallint(5) NOT NULL default '0',
    `role_permission` text NULL,
    `store_ids` text NULL,
    `current_quote_id` int(10) unsigned NULL,
    PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('simipos_session')} (
    `user_id` int(10) unsigned NOT NULL default '0',
    `logdate` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `sessid` varchar(40) NULL,
    `quote_id` int(10) unsigned NULL,
    KEY `SIMIPOS_SESSION_USER_ID` (`user_id`),
    KEY `SIMIPOS_SESSION_SESSID` (`sessid`),
    CONSTRAINT `SIMIPOS_SESSION_USER_ID` FOREIGN KEY (`user_id`) REFERENCES {$this->getTable('simipos_user')} (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('sales/order')}
    ADD COLUMN `simipos_cash` decimal(12,4) NOT NULL default '0',
    ADD COLUMN `simipos_base_cash` decimal(12,4) NOT NULL default '0',
    ADD COLUMN `simipos_user` int(10) unsigned NULL,
    ADD COLUMN `simipos_email` varchar(255) NULL;

ALTER TABLE {$this->getTable('sales/order_grid')}
    ADD COLUMN `simipos_user` int(10) unsigned NULL,
    ADD COLUMN `simipos_email` varchar(255) NULL;

ALTER TABLE {$this->getTable('sales/invoice')}
    ADD COLUMN `simipos_cash` decimal(12,4) NOT NULL default '0',
    ADD COLUMN `simipos_base_cash` decimal(12,4) NOT NULL default '0';

ALTER TABLE {$this->getTable('sales/quote')}
    ADD COLUMN `simi_discount_amount` decimal(12,4) NOT NULL default '0',
    ADD COLUMN `simi_discount_percent` decimal(12,4) NOT NULL default '0',
    ADD COLUMN `simi_discount_desc` varchar(255) NULL;

ALTER TABLE {$this->getTable('sales/quote_item')}
    ADD COLUMN `regular_price` decimal(12,4) NULL;

ALTER TABLE {$this->getTable('customer/entity')}
    ADD COLUMN `telephone` varchar(255) NULL;

");

/**
 * Add and update customer telephone attribute
 */
$setup = new Mage_Customer_Model_Entity_Setup('customer_setup');
$setup->addAttribute('customer', 'telephone', array(
    'type'  => 'static',
    'label' => 'Customer Telephone',
    'sort_order'    => 1000
));

$select = Mage::getResourceModel('customer/customer_collection')
    ->joinAttribute('billing_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
    ->getSelect();
$select->reset(Zend_Db_Select::COLUMNS);
if (strpos($select->__toString(), '_table_default_billing')) {
	$select->columns(array('telephone' => '_table_default_billing.value'));
} else {
    $select->columns(array('telephone' => 'at_billing_telephone.value'));
}

$from = $select->getPart(Zend_Db_Select::FROM);
unset($from['e']);
$select->setPart(Zend_Db_Select::FROM, $from);

$updateSql = $select->crossUpdateFromSelect(array(
    'e' => $installer->getTable('customer/entity'),
));
$installer->getConnection()->query($updateSql);

$installer->endSetup();
