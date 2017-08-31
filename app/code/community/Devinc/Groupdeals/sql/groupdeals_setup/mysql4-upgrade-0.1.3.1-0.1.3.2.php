<?php
$installer = $this;

$installer->startSetup();

//add new groupdeal_datetime attributes
$entityTypeCode = 'catalog_product';
$entityTypeId = $installer->getEntityTypeId($entityTypeCode);
$attributeSetName = 'Group Deal';
$attributeSetId = $installer->getAttributeSetId($entityTypeCode, $attributeSetName);
$productTypes = array('simple', 'virtual', 'configurable');	 

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

//migrate datetime from/to values from previous groupdeals table to product attributes

//check to see if column has already been dropped
$datetimeFromColumn = $installer->getConnection()->tableColumnExists('groupdeals', 'datetime_from', null);
$datetimeToColumn = $installer->getConnection()->tableColumnExists('groupdeals', 'datetime_to', null);

if ($datetimeFromColumn && $datetimeToColumn) {
	$groupdealCollection = Mage::getModel('groupdeals/groupdeals')->getCollection();
	$attributeId['groupdeal_datetime_from'] = $this->getAttribute($entityTypeCode, 'groupdeal_datetime_from', 'attribute_id');
	$attributeId['groupdeal_datetime_to'] = $this->getAttribute($entityTypeCode, 'groupdeal_datetime_to', 'attribute_id');
	$storeId = 0;
	$values = array();
	
	foreach ($groupdealCollection as $groupdeal) {
		$productId = $groupdeal->getProductId();
		$value['datetime_from'] = $groupdeal->getDatetimeFrom();
		$value['datetime_to'] = $groupdeal->getDatetimeTo();
		
		if ($productId!=0) {
			$values[] = "(".$entityTypeId.",".$attributeId["groupdeal_datetime_from"].",".$storeId.",".$productId.",'".$value['datetime_from']."')";
			$values[] = "(".$entityTypeId.",".$attributeId['groupdeal_datetime_to'].",".$storeId.",".$productId.",'".$value['datetime_to']."')";
		}
	}
	
	if (count($values)) {
		$valuesString = implode(',',$values);	
		$installer->run("insert into {$installer->getTable('catalog_product_entity_datetime')} (`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values ".$valuesString.";");
	}
}

//drop previous datetime columns + sold_qty column
$installer->getConnection()->dropColumn($installer->getTable('groupdeals'), 'datetime_from');
$installer->getConnection()->dropColumn($installer->getTable('groupdeals'), 'datetime_to');
$installer->getConnection()->dropColumn($installer->getTable('groupdeals'), 'sold_qty');
$installer->getConnection()->addColumn($installer->getTable('groupdeals'), 'position', 'int(11)');

//Create default configuration
$installer->setConfigData('groupdeals/configuration/gift_to_friend',	    		1);

//refresh previous groupdeals for status changes
//Mage::getModel('groupdeals/groupdeals')->refreshGroupdeals();

$installer->endSetup(); 
