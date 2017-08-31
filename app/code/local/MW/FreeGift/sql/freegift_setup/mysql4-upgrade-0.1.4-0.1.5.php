<?php
$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$sql = "";

$sql .= "
            ALTER TABLE {$installer->getTable('freegift/salesrule')}
            CHANGE `from_date` `from_date` VARCHAR( 10 ) NULL DEFAULT NULL ,
            CHANGE `to_date` `to_date` VARCHAR( 10 ) NULL DEFAULT NULL;
            ";
$sql .= "
            ALTER TABLE {$installer->getTable('freegift/rule')}
            CHANGE `from_date` `from_date` VARCHAR( 10 ) NULL DEFAULT NULL ,
            CHANGE `to_date` `to_date` VARCHAR( 10 ) NULL DEFAULT NULL;
            ";

$installer->run($sql);

$installer->endSetup();