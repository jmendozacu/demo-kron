<?php
/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$installer->addAttribute('catalog_product', 'tradein_enable', array(
    'group'         				=> 'MageDevGroup Attributes',
    'input'         				=> 'select',
    'type'          				=> 'int',
    'label'         				=> 'TradeIn Enable',
    'class'             			=> '',
    'frontend'						=> '',
    'backend'       				=> '',
    'default'                       => '1',
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
    'global'        				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'sort_order'                    => 6000,
));

$installer->endSetup();
