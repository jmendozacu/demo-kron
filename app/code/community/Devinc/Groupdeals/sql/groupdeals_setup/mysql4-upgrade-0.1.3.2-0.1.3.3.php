<?php
$installer = $this;

$installer->startSetup();

//set new slideshow default value
$installer->setConfigData('groupdeals/configuration/slideshow_effect',				'kb');

//update groupdeal attributes to apply to bundle products as well
$entityTypeCode = 'catalog_product';
$productTypes = array('simple', 'virtual', 'configurable', 'bundle');	 
$productTypesString = implode (',', $productTypes);

$installer->updateAttribute($entityTypeCode, 'groupdeal_datetime_from', 'apply_to', $productTypesString);
$installer->updateAttribute($entityTypeCode, 'groupdeal_datetime_to', 'apply_to', $productTypesString);
$installer->updateAttribute($entityTypeCode, 'groupdeal_status', 'apply_to', $productTypesString);
$installer->updateAttribute($entityTypeCode, 'groupdeal_highlights', 'apply_to', $productTypesString);
$installer->updateAttribute($entityTypeCode, 'groupdeal_fineprint', 'apply_to', $productTypesString);

//create new country/region/city table
$installer->run("
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
    ");

//migrate country/region/city data to the new table
$groupdealCollection = Mage::getModel('groupdeals/groupdeals')->getCollection();
$values = array();
foreach ($groupdealCollection as $groupdeal) {
    $groupdealId = $groupdeal->getId();
    $productId = $groupdeal->getProductId();
    $countryId = $groupdeal->getCountryId();
    $region = $groupdeal->getRegion();			
    $city = $groupdeal->getCity();
    		
    if ($productId!=0) {
    	$values[] = "(".$groupdealId.",".$productId.",'".$countryId."','".$region."','".$city."')";
    }
}

if (count($values)) {
	$valuesString = implode(',',$values);
	$installer->run("insert into {$installer->getTable('groupdeals_crc')} (`groupdeals_id`,`product_id`,`country_id`,`region`,`city`) values ".$valuesString.";");
}

$installer->getConnection()->dropColumn($installer->getTable('groupdeals'), 'country_id');
$installer->getConnection()->dropColumn($installer->getTable('groupdeals'), 'region');
$installer->getConnection()->dropColumn($installer->getTable('groupdeals'), 'city');

//set previous homepage path
$previousPath = Mage::getStoreConfig('groupdeals/previous_homepage_path');
if (isset($previousPath) && $previousPath!='') {
	$installer->setConfigData('web/default/front', $previousPath);
}

// Add city/region columns to sales/quote items table
$installer->getConnection()->addColumn($installer->getTable('sales/quote_item'), 'crc_id', 'int(10)');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'crc_id', 'int(10)');

$installer->endSetup();