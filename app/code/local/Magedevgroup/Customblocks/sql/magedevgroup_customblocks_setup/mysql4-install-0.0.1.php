<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();


$installer->getConnection()->insertMultiple(
    $installer->getTable('admin/permission_block'),
    array(
        array('block_name' => 'magedevgroup_customblocks/orders', 'is_allowed' => 1),
    )
);

$installer->endSetup();