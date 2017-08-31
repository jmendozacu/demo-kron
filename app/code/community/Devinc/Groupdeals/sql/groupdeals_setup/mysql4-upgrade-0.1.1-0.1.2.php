<?php
$installer = $this;

$installer->startSetup();

// alter tables
$installer->getConnection()->addColumn($installer->getTable('groupdeals'), 'datetime_from', 'datetime after `city`');
$installer->getConnection()->addColumn($installer->getTable('groupdeals'), 'datetime_to', 'datetime after `datetime_from`');

//migrate date from/to and time from/to values to datetime from/to column
$groupdealCollection = Mage::getModel('groupdeals/groupdeals')->getCollection();

foreach ($groupdealCollection as $groupdeal) {	
	$value['datetime_from'] = $groupdeal->getDateFrom().' '.str_replace(',',':',$groupdeal->getTimeFrom());
	$value['datetime_to'] = $groupdeal->getDateTo().' '.str_replace(',',':',$groupdeal->getTimeTo());
	
	$groupdeal->setDatetimeFrom($value['datetime_from']);
	$groupdeal->setDatetimeTo($value['datetime_to']);
	$groupdeal->save();
}

//drop previous columns
$installer->getConnection()->dropColumn($installer->getTable('groupdeals'), 'date_from');
$installer->getConnection()->dropColumn($installer->getTable('groupdeals'), 'date_to');
$installer->getConnection()->dropColumn($installer->getTable('groupdeals'), 'time_from');
$installer->getConnection()->dropColumn($installer->getTable('groupdeals'), 'time_to');

$installer->endSetup(); 