<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('themeone_banner'), 'type', 'smallint(5) unsigned default 3');
$installer->getConnection()->addColumn($installer->getTable('themeone_banner'), 'category_id', 'int(10) unsigned  NOT NULL');
$installer->getConnection()->addColumn($installer->getTable('themeone_banner'), 'product_id', 'int(10) unsigned  NOT NULL');

$installer->endSetup();
