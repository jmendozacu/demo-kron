<?php
/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');

$installer->startSetup();

$installer->addAttribute('magedevgroup_tradein_tradeinproposal', 'discount_type', array(
    'type'             => 'int',
    'backend'          => '',
    'frontend'         => '',
    'label'            => 'Discount Type',
    'input'            => 'select',
    'class'            => '',
    'source'           => 'magedevgroup_tradein/entity_attribute_source_discountType',
    'global'           => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'          => true,
    'required'         => true,
    'user_defined'     => true,
    'default'          => '',
    'searchable'       => false,
    'filterable'       => false,
    'comparable'       => false,
    'visible_on_front' => false,
    'unique'           => false
));

$installer->endSetup();
