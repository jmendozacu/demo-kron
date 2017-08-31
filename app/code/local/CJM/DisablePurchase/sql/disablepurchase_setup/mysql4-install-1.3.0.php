<?php

$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('catalog_product', 'disabledtext', array(

    'group'         				=> 'Disable Purchase',
    'input'         				=> 'text',
    'type'          				=> 'varchar',
    'label'         				=> 'Disabled Text',
    'backend'       				=> '',
	'frontend'						=> '',
    'visible'       				=> true,
    'required'      				=> false,
    'user_defined' 					=> true,
    'searchable' 					=> false,
    'filterable' 					=> false,
    'comparable'    				=> false,
    'visible_on_front' 				=> false,
    'visible_in_advanced_search'  	=> false,
    'is_html_allowed_on_front' 		=> false,
    'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$setup->addAttribute('catalog_product', 'purchasedisabled', array(

    'group'         				=> 'Disable Purchase',
    'input'         				=> 'select',
    'type'          				=> 'int',
    'label'         				=> 'Disable Purchase?',
	'class'             			=> '',
	'frontend'						=> '',
    'backend'       				=> '',
	'source'            			=> 'eav/entity_attribute_source_boolean',
    'visible'       				=> true,
    'required'      				=> false,
    'user_defined' 					=> true,
    'searchable' 					=> false,
    'filterable' 					=> false,
    'comparable'    				=> false,
    'visible_on_front' 				=> true,
    'visible_in_advanced_search'  	=> false,
    'is_html_allowed_on_front' 		=> false,
    'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));

$installer->endSetup();