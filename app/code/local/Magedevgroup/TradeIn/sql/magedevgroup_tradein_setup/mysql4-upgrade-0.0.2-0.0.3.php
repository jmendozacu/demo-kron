<?php
/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$installer->updateAttribute('magedevgroup_tradein_tradeinproposal','phone','backend_type','text');
$installer->updateAttribute('magedevgroup_tradein_tradeinproposal','condition','source_model','magedevgroup_tradein/entity_attribute_source_condition');

$installer->removeAttribute('magedevgroup_tradein_tradeinproposal','discount_percentage');

$installer->addAttribute('magedevgroup_tradein_tradeinproposal', 'discount_amount', array(
    'type'             => 'decimal',
    'backend'          => '',
    'frontend'         => '',
    'label'            => 'Discount amount',
    'input'            => 'text',
    'class'            => '',
    'source'           => '',
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
