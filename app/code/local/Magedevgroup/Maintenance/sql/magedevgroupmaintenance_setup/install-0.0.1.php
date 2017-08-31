<?php
/**
 * Created by magedevgroup.com
 */
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Pdo_Mysql */

$installer->startSetup();
$connection->insert($installer->getTable('cms/block'), array(
    'title'             => 'Maintenance Info Block ',
    'identifier'        => 'home-maintance-block',
    'content'           => '<div></div>',
    'creation_time'     => now(),
    'update_time'       => now(),
));
$connection->insert($installer->getTable('cms/block_store'), array(
    'block_id'   => $connection->lastInsertId(),
    'store_id'  => 0
));
$installer->endSetup();