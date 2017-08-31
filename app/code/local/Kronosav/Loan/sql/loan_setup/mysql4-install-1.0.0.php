<?php
$installer = $this;
$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('loan/loan')};
CREATE TABLE {$this->getTable('loan/loan')} (
  `loan_id` int(11) unsigned  AUTO_INCREMENT,
  `loan_from_date` date NOT NULL,
  `loan_to_date` date NOT NULL,
 
  `deposit_amount` int NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `status` text NOT NULL,
  `created_at` timestamp NOT NULL,
   PRIMARY KEY (`loan_id`),
   KEY `FK_LOAN_ENTITY_ID` (`entity_id`),
	CONSTRAINT `FK_LOAN_ENTITY_ID` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('customer_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
   ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
   DROP TABLE IF EXISTS {$this->getTable('loan/product')};
	CREATE TABLE {$this->getTable('loan/product')} (
  `entity_id` int(11) unsigned AUTO_INCREMENT,
  `loan_id` int(11) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
   PRIMARY KEY (`entity_id`),
   KEY `FK_PRODUCT_LOAN_ID` (`loan_id`),
   KEY `FK_PRODUCT_PRODUCT_ID` (`product_id`),
   CONSTRAINT `FK_PRODUCT_LOAN_ID` FOREIGN KEY (`loan_id`) REFERENCES `{$installer->getTable('loan/loan')}` (`loan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
   CONSTRAINT `FK_PRODUCT_PRODUCT_ID` FOREIGN KEY (`product_id`) REFERENCES `{$installer->getTable('catalog_product_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
   ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
");
$installer->endSetup(); 
?>