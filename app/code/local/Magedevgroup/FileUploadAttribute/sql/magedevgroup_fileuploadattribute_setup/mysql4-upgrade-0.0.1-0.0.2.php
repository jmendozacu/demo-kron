<?php
/**
 * @package    Magedevgroup_FileUploadAttribute
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$installer->addAttribute('catalog_product', 'file1_upload_label', array(
    'group' => 'MageDevGroup Attributes',
    'label' => 'File1 Upload Label',
    'type' => 'varchar',
    'input' => 'text',
    'frontend_class' => '',
    'backend' => '',
    'frontend' => '',
    'visible' => true,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'is_html_allowed_on_front' => false,
    'required' => false,
    'used_in_product_listing' => false,
    'sort_order' => 1010,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
));

$installer->addAttribute('catalog_product', 'file2_upload_label', array(
    'group' => 'MageDevGroup Attributes',
    'label' => 'File2 Upload Label',
    'type' => 'varchar',
    'input' => 'text',
    'frontend_class' => '',
    'backend' => '',
    'frontend' => '',
    'visible' => true,
    'user_defined' => false,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'is_html_allowed_on_front' => false,
    'required' => false,
    'used_in_product_listing' => false,
    'sort_order' => 2010,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE,
));

$installer->endSetup();
