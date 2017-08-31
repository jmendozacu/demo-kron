<?php
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('groupdeals_merchants'), 'user_id', 'int(11) after `merchants_id`');
$installer->getConnection()->addColumn($installer->getTable('groupdeals_merchants'), 'permissions', 'varchar(255) after `user_id`');

$installer->endSetup(); 