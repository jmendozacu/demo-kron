<?php

$installer = $this;
$installer->startSetup();
try{
    if(!Mage::helper('freegift/sql')->checkTableStatus('mw_freegift_product_attribute')){
        $table = $installer->getConnection()
            ->newTable($installer->getTable('freegift/product_attribute'))
            ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'auto_increment' => true,
                'length' => 10,
            ), 'Id')
            ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable'  => false,
                'primary'   => true,
                'length' => 10,
            ), 'website_id')
            ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                'nullable'  => false,
                'length' => 5,
                'primary'   => true,
            ), 'customer_group_id')
            ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                'nullable'  => false,
                'length' => 5,
                'primary'   => true,
            ), 'attribute_id')
        ;

        $installer->getConnection()->createTable($table);
    }
}catch (Exception $e){}


$installer->endSetup();