<?php
/**
 * Velan Info Services India Pvt Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.velanapps.com/License.txt
 *
  /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 * *************************************** */
/* This package designed for Magento COMMUNITY edition
 * Velan Info Services does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Velan Info Services does not provide extension support in case of
 * incorrect edition usage.
  /***************************************
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 * ****************************************************
 * @category   velanapps
 * @package    EzLogin
 * @author     Velan Team
 * @copyright  Copyright (c) 2012 - 2013 Velan Info Services India Pvt Ltd. (http://www.velanapps.com)
 * @license    http://store.velanapps.com/License.txt
 */
$installer = $this;

$installer->startSetup();

$installer->run("
					CREATE TABLE IF NOT EXISTS {$this->getTable('ezlogin_accounts')} (
					  `account_id` tinyint(4) NOT NULL AUTO_INCREMENT,
					  `account_name` varchar(255) NOT NULL,
					  PRIMARY KEY (`account_id`)
					) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

					INSERT INTO {$this->getTable('ezlogin_accounts')} (`account_id`, `account_name`) VALUES
					(1, 'Facebook'),
					(2, 'Twitter'),
					(3, 'Google'),
					(4, 'Yahoo'),
					(5, 'LinkedIn');

					CREATE TABLE IF NOT EXISTS {$this->getTable('ezlogin_customers')} (
					  `customer_id` tinyint(4) NOT NULL AUTO_INCREMENT,
					  `mage_customer_id` varchar(255) NOT NULL,
					  `account_id` varchar(255) NOT NULL,
					  `social_id` varchar(255) NOT NULL,
					  `profile_image` varchar(255) NOT NULL,
					  `is_verified` varchar(255) NOT NULL,
					  PRIMARY KEY (`customer_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

					INSERT INTO {$this->getTable('core_url_rewrite')} (`url_rewrite_id`, `store_id`, `id_path`, `request_path`, `target_path`, `is_system`, `options`, `description`, `category_id`, `product_id`) VALUES
(NULL, 1, 'add-facebook', 'add-facebook', 'ezlogin/social/connect/account/facebook', 1, NULL, NULL, NULL, NULL),
(NULL, 1, 'add-twitter', 'add-twitter', 'ezlogin/social/connect/account/twitter', 1, NULL, NULL, NULL, NULL),
(NULL, 1, 'add-google', 'add-google', 'ezlogin/social/connect/account/google', 1, NULL, NULL, NULL, NULL),
(NULL, 1, 'add-yahoo', 'add-yahoo', 'ezlogin/social/connect/account/yahoo', 1, NULL, NULL, NULL, NULL),
(NULL, 1, 'add-linkedin', 'add-linkedin', 'ezlogin/social/connect/account/linkedin', 1, NULL, NULL, NULL, NULL);
					
				");

$installer->endSetup();