<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('wk_preorder')};
CREATE TABLE {$this->getTable('wk_preorder')} (
    `preorder_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `orderid` int(11) NOT NULL,
    `itemid` int(11) NOT NULL,
    `childid` int(11) NOT NULL,
    `rand` int(11) NOT NULL,
    `qty` int(11) NOT NULL,
    `paid_amount` float NOT NULL,
    `remaining_amount` float NOT NULL,
    `ref_number` varchar(255) NOT NULL,
    `status` int(11) NOT NULL,
    `type` int(11) NOT NULL,
    `preorder_percent` float NOT NULL,
    `customer_id` int(11) NOT NULL,
    `notify` int(11) NOT NULL,
    `time` varchar(255) NOT NULL,
    PRIMARY KEY (`preorder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


UPDATE {$this->getTable('core_config_data')} SET value=1 where path = 'cataloginventory/options/show_out_of_stock';

    ");

$setup = new Mage_Eav_Model_Entity_Setup('core_setup'); 
$setup->addAttribute('catalog_product', 'wk_preorder', array(
             'label'             => 'Preorder status',
             'type'              => 'varchar',
             'group'             => 'General',
             'input'             => 'select',
             'backend'           => 'eav/entity_attribute_backend_array',
             'frontend'          => '',
             'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
             'visible'           => true,
             'required'          => false,
             'user_defined'      => true,
             'searchable'        => false,
             'filterable'        => false,
             'comparable'        => false,
             'option'            => array('value' => array('one' => array('Disable'),'two' => array('Enable'))),
             'visible_on_front'  => false,
             'visible_in_advanced_search' => false,
             'unique'            => false
));

$setup->addAttribute('catalog_product', 'wk_availability', array(
             'label'             => 'Preorder Product Availability Date',
             'type'              => 'varchar',
             'group'             => 'General',
             'input'             => 'date',
             'backend'           => 'eav/entity_attribute_backend_datetime',
             'frontend'          => '',
             'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
             'visible'           => true,
             'required'          => false,
             'user_defined'      => true,
             'searchable'        => false,
             'filterable'        => false,
             'comparable'        => false,
             'visible_on_front'  => false,
             'visible_in_advanced_search' => false,
             'unique'            => false
));

$installer->endSetup(); 