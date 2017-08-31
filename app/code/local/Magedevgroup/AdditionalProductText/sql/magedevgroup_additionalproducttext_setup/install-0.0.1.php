<?php
/**
 * @package    Magedevgroup_AdditionalProductText
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$installer->addAttribute('catalog_product', 'additional_product_text', [
    'group'                      => 'MageDevGroup Attributes',
    'label'                      => 'Additional Product Text',
    'type'                       => 'text',
    'input'                      => 'text',
    'frontend_class'             => '',
    'backend'                    => '',
    'frontend'                   => '',
    'visible'                    => true,
    'user_defined'               => false,
    'searchable'                 => false,
    'filterable'                 => false,
    'comparable'                 => false,
    'visible_on_front'           => false,
    'visible_in_advanced_search' => false,
    'is_html_allowed_on_front'   => false,
    'required'                   => false,
    'used_in_product_listing'    => true,
    'sort_order'                 => 5000,
    'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
]);

$installer->endSetup();