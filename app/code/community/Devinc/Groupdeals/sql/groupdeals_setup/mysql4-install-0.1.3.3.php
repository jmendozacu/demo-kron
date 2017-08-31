<?php
$installer = $this;

$installer->startSetup();

// Add Attributes
//create the groupdeal attribute set + it's attributes
Mage::getModel('groupdeals/groupdeals')->addGroupdealAttributeSet($installer);

//add groupdeal_datetime attributes
$entityTypeCode = 'catalog_product';
$entityTypeId = $installer->getEntityTypeId($entityTypeCode);
$attributeSetName = 'Group Deal';
$attributeSetId = $installer->getAttributeSetId($entityTypeCode, $attributeSetName);
$productTypes = array('simple', 'virtual', 'configurable', 'bundle');	 

$data['groupdeal_datetime_from'] = array(
            'frontend_label'  				=> 'Date/Time From',
    		'attribute_code'  				=> 'groupdeal_datetime_from',
            'is_global'       				=> 1,    
            'frontend_input'  				=> 'date',
		    'default_value'   				=> '',     
            'is_unique'       				=> 0,
            'is_required'     				=> 1,
    		'apply_to'        				=> $productTypes,
            'is_searchable'   				=> 0,      
            'is_visible_in_advanced_search' => 1,   
            'used_in_product_listing'		=> 1,           
            'is_comparable'     			=> 1,         
            'is_wysiwyg_enabled'     		=> 0,  
		    'backend_model'    	 			=> 'groupdeals/source_datetime', 
            'is_user_defined'				=> 0,
            
        );
if (!$installer->getAttributeId($entityTypeCode, $data['groupdeal_datetime_from']['attribute_code'])) {
    Mage::getModel('groupdeals/groupdeals')->addAttribute($data['groupdeal_datetime_from'], $entityTypeId);
    $installer->addAttributeToSet($entityTypeCode, $attributeSetId, 'General', $data['groupdeal_datetime_from']['attribute_code']);
}
	
$data['groupdeal_datetime_to'] = array(
            'frontend_label'  				=> 'Date/Time To',
    		'attribute_code'  				=> 'groupdeal_datetime_to',
            'is_global'       				=> 1,    
            'frontend_input'  				=> 'date',
		    'default_value'   				=> '',     
            'is_unique'       				=> 0,
            'is_required'     				=> 1,
    		'apply_to'        				=> $productTypes,
            'is_searchable'   				=> 0,      
            'is_visible_in_advanced_search' => 1,        
            'used_in_product_listing'		=> 1,         
            'is_comparable'     			=> 1,         
            'is_wysiwyg_enabled'     		=> 0,  
		    'backend_model'    	 			=> 'groupdeals/source_datetime', 
            'is_user_defined'				=> 0,
        );
if (!$installer->getAttributeId($entityTypeCode, $data['groupdeal_datetime_to']['attribute_code'])) {
    Mage::getModel('groupdeals/groupdeals')->addAttribute($data['groupdeal_datetime_to'], $entityTypeId);
    $installer->addAttributeToSet($entityTypeCode, $attributeSetId, 'General', $data['groupdeal_datetime_to']['attribute_code']);
}	

//add facebook connect attribute
$installer->addAttribute('customer', 'facebook_uid', array(
	    'type'	 => 'varchar',
	    'label'		=> 'Facebook Uid',
	    'visible'   => false,
		'required'	=> false
));

// Add Tables
$installer->run("

DROP TABLE IF EXISTS {$installer->getTable('groupdeals')};
CREATE TABLE {$installer->getTable('groupdeals')} (
  `groupdeals_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `merchant_id` int(11) NOT NULL,
  `minimum_qty` int(11) NOT NULL,
  `maximum_qty` int(11) NOT NULL,
  `target_met_email` int(11) NOT NULL,
  `coupon_barcode` text NOT NULL default '',
  `coupon_merchant_address` int(11) NOT NULL,
  `coupon_merchant_contact` int(11) NOT NULL,
  `coupon_expiration_date` date NULL,
  `coupon_price` int(11) NOT NULL,
  `coupon_fine_print` int(11) NOT NULL,
  `coupon_highlights` int(11) NOT NULL,
  `coupon_merchant_description` int(11) NOT NULL,
  `coupon_business_hours` int(11) NOT NULL,
  `coupon_merchant_logo` int(11) NOT NULL,
  `coupon_additional_info` text NOT NULL default '',
  `position` int(11) NOT NULL,
  PRIMARY KEY (`groupdeals_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$installer->getTable('groupdeals_crc')};
CREATE TABLE {$installer->getTable('groupdeals_crc')} (
  `crc_id` int(11) unsigned NOT NULL auto_increment,
  `groupdeals_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `country_id` varchar(255) NOT NULL default '',
  `region` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  PRIMARY KEY (`crc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$installer->getTable('groupdeals_merchants')};
CREATE TABLE {$installer->getTable('groupdeals_merchants')} (
  `merchants_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `permissions` varchar(255) NOT NULL default '',
  `name` text NOT NULL default '',
  `merchant_logo` text NOT NULL default '',
  `description` text NOT NULL default '',
  `website` text NOT NULL default '',
  `email` text NOT NULL default '',
  `facebook` text NOT NULL default '',
  `twitter` text NOT NULL default '',
  `phone` text NOT NULL default '',
  `mobile` text NOT NULL default '',
  `business_hours` text NOT NULL default '',
  `address` text NOT NULL default '',
  `redeem` text NOT NULL default '',
  `paypal_email` varchar(255) NOT NULL default '',
  `authorize_info` text NOT NULL default '',
  `bank_info` text NOT NULL default '',
  `other` text NOT NULL default '',
  `status` int(11) NOT NULL,
  PRIMARY KEY (`merchants_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$installer->getTable('groupdeals_subscribers')};
CREATE TABLE {$installer->getTable('groupdeals_subscribers')} (
  `subscriber_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL default '',
  `city` varchar(255) NOT NULL default '',
  PRIMARY KEY (`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$installer->getTable('groupdeals_notifications')};
CREATE TABLE {$installer->getTable('groupdeals_notifications')} (
  `notification_id` int(11) unsigned NOT NULL auto_increment,
  `groupdeals_id` int(11) NOT NULL,
  `website_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL default '',
  `unnotified_subscriber_ids` text NOT NULL default '',
  `notified_subscriber_ids` text NOT NULL default '',
  `status` varchar(255) NOT NULL default '',
  PRIMARY KEY (`notification_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$installer->getTable('groupdeals_coupons')};
CREATE TABLE {$installer->getTable('groupdeals_coupons')} (
  `coupon_id` int(11) unsigned NOT NULL auto_increment,
  `groupdeals_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `coupon_code` varchar(255) NOT NULL default '',
  `redeem` varchar(255) NOT NULL default '',
  `status` varchar(255) NOT NULL default '',
  PRIMARY KEY (`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

// Add city/region columns to sales/quote items table
$installer->getConnection()->addColumn($installer->getTable('sales/quote_item'), 'crc_id', 'int(10)');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'crc_id', 'int(10)');

// Add gift option columns to sales/quote table
$installer->getConnection()->addColumn($installer->getTable('sales/quote'), 'groupdeals_coupon_from', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales/quote'), 'groupdeals_coupon_to', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales/quote'), 'groupdeals_coupon_to_email', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales/quote'), 'groupdeals_coupon_message', 'varchar(255)');

$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'groupdeals_coupon_from', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'groupdeals_coupon_to', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'groupdeals_coupon_to_email', 'varchar(255)');
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'groupdeals_coupon_message', 'varchar(255)');

//Create default configuration
$installer->setConfigData('groupdeals/configuration/enabled',						0);
$installer->setConfigData('groupdeals/configuration/homepage_deals',	    		'default');
$installer->setConfigData('groupdeals/configuration/deals_view',					0);
$installer->setConfigData('groupdeals/configuration/list_type',						0);
$installer->setConfigData('groupdeals/configuration/subscribe_popup',				1);
$installer->setConfigData('groupdeals/configuration/gift_to_friend',	    		1);
$installer->setConfigData('groupdeals/configuration/countdown_type',	    		1);
$installer->setConfigData('groupdeals/configuration/slideshow_effect',				'kb');
$installer->setConfigData('groupdeals/configuration/enable_facebook_comments',		1);
$installer->setConfigData('groupdeals/configuration/sidedeals_number',	    		'3');
$installer->setConfigData('groupdeals/configuration/display_upcoming',				1);
$installer->setConfigData('groupdeals/configuration/coupons_sender',				'general');

$installer->setConfigData('groupdeals/countdown_configuration/display_days',	    0);
$installer->setConfigData('groupdeals/countdown_configuration/bg_main',	   			'#F6F6F6');
$installer->setConfigData('groupdeals/countdown_configuration/bg_color',	   		'#333333');
$installer->setConfigData('groupdeals/countdown_configuration/alpha',	   			'70');
$installer->setConfigData('groupdeals/countdown_configuration/textcolor',	   		'#FFFFFF');
$installer->setConfigData('groupdeals/countdown_configuration/txt_color',	   		'#333333');
$installer->setConfigData('groupdeals/countdown_configuration/sec_text',	   		'SECONDS');
$installer->setConfigData('groupdeals/countdown_configuration/min_text',	   		'MINUTES');
$installer->setConfigData('groupdeals/countdown_configuration/hour_text',	   		'HOURS');
$installer->setConfigData('groupdeals/countdown_configuration/days_text',	   		'DAYS');

$installer->setConfigData('groupdeals/js_countdown_configuration/textcolor',		'#333333');
$installer->setConfigData('groupdeals/js_countdown_configuration/days_text',		'day(s)');

$installer->setConfigData('groupdeals/merchants_subscribe/enabled',					1);
$installer->setConfigData('groupdeals/merchants_subscribe/facebook_link',	   		1);
$installer->setConfigData('groupdeals/merchants_subscribe/twitter_link',			1);
$installer->setConfigData('groupdeals/merchants_subscribe/bussiness_hours',			1);
$installer->setConfigData('groupdeals/merchants_subscribe/address',					2);
$installer->setConfigData('groupdeals/merchants_subscribe/redeem',					1);

$installer->setConfigData('groupdeals/facebook_connect/enabled',					0);
$installer->setConfigData('groupdeals/facebook_connect/locale',				    	'en_US');

$installer->setConfigData('groupdeals/notifications/email_sender',	   				'general');
$installer->setConfigData('groupdeals/notifications/email_new_deal',	   			1);
$installer->setConfigData('groupdeals/notifications/email_new_deal_template',	    'groupdeals_notifications_email_new_deal_template');
$installer->setConfigData('groupdeals/notifications/email_target_met',	   			1);
$installer->setConfigData('groupdeals/notifications/email_target_met_template',	   	'groupdeals_notifications_email_target_met_template');
$installer->setConfigData('groupdeals/notifications/email_deal_over',	   			1);
$installer->setConfigData('groupdeals/notifications/email_deal_over_template',	   	'groupdeals_notifications_email_deal_over_template');

$installer->setConfigData('system/cron/schedule_generate_every',	   				1);
$installer->setConfigData('system/cron/schedule_ahead_for',	   						1);
$installer->setConfigData('system/cron/schedule_lifetime',	   						30);
$installer->setConfigData('system/cron/history_cleanup_every',	   					120);
$installer->setConfigData('system/cron/history_success_lifetime',	   				120);
$installer->setConfigData('system/cron/history_failure_lifetime',	   				120);

$installer->endSetup(); 