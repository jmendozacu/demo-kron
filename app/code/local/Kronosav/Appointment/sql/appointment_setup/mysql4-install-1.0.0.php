<?php
$installer = $this;
$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('appointment/appointment')};
CREATE TABLE {$this->getTable('appointment/appointment')} (
  `appointment_id` int(11) unsigned  AUTO_INCREMENT,
  `appointment_description` text NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `appointment_date` datetime NOT NULL,
  `status` int(10) unsigned NOT NULL,
  `location` text NOT NULL,
  `address` text NOT NULL,
  `appointment_time` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL,
   PRIMARY KEY (`appointment_id`),
   KEY `FK_APPOINTMENT_ENTITY_ID` (`entity_id`),
   CONSTRAINT `FK_APPOINTMENT_ENTITY_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('customer_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
");
$installer->endSetup(); 
?>