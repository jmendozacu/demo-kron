<?php
/**
 * @package    Magedevgroup_ScrappageScheme
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Core_Model_Resource_Setup $installer */

$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('scrappagescheme/scrap'))
    ->addColumn('scrap_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'primary' => true,
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
        )
    )
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable' => false,
        )
    )
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable' => false,
        )
    )
    ->addColumn('percentage', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
        )
    )
    ->addColumn('scrap_status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
        )
    );

$installer->getConnection()->createTable($table);

$installer->endSetup();
?>
