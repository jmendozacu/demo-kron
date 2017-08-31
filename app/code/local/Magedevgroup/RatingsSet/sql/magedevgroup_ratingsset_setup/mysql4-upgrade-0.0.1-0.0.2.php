<?php
/**
 * @package    Magedevgroup_RatingsSet
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$installer->addAttribute('catalog_product', 'ratings_set', array(
    'label'             => 'Ratings Set',
    'type'              => 'int',	//backend_type
    'input'             => 'select',	//frontend_input
    'frontend_class'	=> '',
    'source'			=> 'magedevgroup_ratingsset/attribute_source_ratingSets',
    'backend'           => '',
    'frontend'          => '',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
    'required'          => true,
    'used_in_product_listing'	=> false,
    'sort_order' => 1000,
));

$installer->endSetup();