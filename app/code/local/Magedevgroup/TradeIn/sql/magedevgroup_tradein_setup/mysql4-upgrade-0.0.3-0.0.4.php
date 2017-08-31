<?php
/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Catalog_Model_Resource_Setup $installer */
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');

$installer->startSetup();

$installer->addAttribute('magedevgroup_tradein_tradeinproposal', 'pay4later_months', array(
    'type'             => 'int',
    'backend'          => '',
    'frontend'         => '',
    'label'            => 'Pay4Later months',
    'input'            => 'select',
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

$installer->addAttribute('magedevgroup_tradein_tradeinproposal', 'pay4later_deposit', array(
    'type'             => 'int',
    'backend'          => '',
    'frontend'         => '',
    'label'            => 'Pay4Later deposit',
    'input'            => 'select',
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
