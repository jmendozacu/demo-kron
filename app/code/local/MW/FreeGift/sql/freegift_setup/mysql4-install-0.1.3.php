<?php
$installer = $this;
$installer->startSetup();

$resource   = Mage::getSingleton('core/resource');

if(!Mage::helper('freegift/sql')->checkTableStatus('mw_freegift_product')){
    $table = $installer->getConnection()
        ->newTable($installer->getTable('freegift/product'))
        ->addColumn('rule_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            'auto_increment' => true,
            'length' => 10,
        ), 'Id')
        ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'length' => 10,
        ), 'rule_id')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'length' => 10,
        ), 'product_id')
        ->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
            'nullable'  => false,
        ), 'from_date')
        ->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
            'nullable'  => false,
        ), 'to_date')
        ->addColumn('stop_rules_processing', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'nullable'  => false,
            'length' => 1,
        ), 'stop_rules_processing')
        ->addColumn('discount_qty', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'length' => 10,
        ), 'discount_qty')
        ->addColumn('times_used', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'length' => 10,
        ), 'times_used')
        ->addColumn('customer_group_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
        ), 'customer_group_ids')
        ->addColumn('website_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
        ), 'website_ids')
        ->addColumn('gift_product_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
        ), 'gift_product_ids')
        ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'length' => 10,
        ), 'sort_order');
    $installer->getConnection()->createTable($table);
}
if(!Mage::helper('freegift/sql')->checkTableStatus('mw_freegift_rule')){
    $table = $installer->getConnection()
        ->newTable($installer->getTable('freegift/rule'))
        ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            'auto_increment' => true,
            'length' => 10,
        ), 'Id')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable'  => false,
            'length' => 255,
        ), 'name')
        ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
        ), 'description')
        ->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
            'nullable'  => false,
        ), 'from_date')
        ->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
            'nullable'  => false,
        ), 'to_date')
        ->addColumn('customer_group_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
        ), 'customer_group_ids')
        ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'nullable'  => false,
            'length' => 1,
        ), 'is_active')
        ->addColumn('discount_qty', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'length' => 10,
        ), 'discount_qty')
        ->addColumn('times_used', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'length' => 10,
        ), 'times_used')
        ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
        ), 'conditions_serialized')
        ->addColumn('stop_rules_processing', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'nullable'  => false,
            'length' => 1,
        ), 'stop_rules_processing')
        ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'length' => 10,
        ), 'sort_order')
        ->addColumn('simple_action', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable'  => false,
            'length' => 255,
        ), 'simple_action')
        ->addColumn('gift_product_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
        ), 'gift_product_ids')
        ->addColumn('website_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
        ), 'website_ids')
    ;
    $installer->getConnection()->createTable($table);
}
if(!Mage::helper('freegift/sql')->checkTableStatus('mw_freegift_salesrule')){
    $table = $installer->getConnection()
        ->newTable($installer->getTable('freegift/salesrule'))
        ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            'auto_increment' => true,
            'length' => 10,
        ), 'Id')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable'  => false,
            'length' => 255,
        ), 'name')
        ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
        ), 'description')
        ->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
            'nullable'  => false,
        ), 'from_date')
        ->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
            'nullable'  => false,
        ), 'to_date')
        ->addColumn('customer_group_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
        ), 'customer_group_ids')
        ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'nullable'  => false,
            'length' => 1,
        ), 'is_active')
        ->addColumn('discount_qty', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'length' => 10,
        ), 'discount_qty')
        ->addColumn('coupon_code', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable'  => false,
            'length' => 255,
        ), 'coupon_code')
        ->addColumn('times_used', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'length' => 10,
        ), 'times_used')
        ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
        ), 'conditions_serialized')
        ->addColumn('stop_rules_processing', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'nullable'  => false,
            'length' => 1,
        ), 'stop_rules_processing')
        ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'length' => 10,
        ), 'sort_order')
        ->addColumn('gift_product_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
        ), 'gift_product_ids')
        ->addColumn('website_ids', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable'  => false,
        ), 'website_ids')
    ;
    $installer->getConnection()->createTable($table);
}
if(!Mage::helper('freegift/sql')->checkColumnExist($resource->getTableName('sales/quote'), 'freegift_coupon_code')){
    $installer->getConnection()->addColumn($installer->getTable('sales/quote'),
        'freegift_coupon_code',
        'varchar(255) default null AFTER `store_id`');
}
if(!Mage::helper('freegift/sql')->checkColumnExist($resource->getTableName('sales/quote'), 'freegift_applied_rule_ids')){
    $installer->getConnection()->addColumn($installer->getTable('sales/quote'),
        'freegift_applied_rule_ids',
        'varchar(255) default null AFTER `store_id`');
}

if(!Mage::helper('freegift/sql')->checkColumnExist($resource->getTableName('sales/quote'), 'freegift_ids')){
    $installer->getConnection()->addColumn($installer->getTable('sales/quote'),
        'freegift_ids',
        'varchar(255) default null AFTER `store_id`');
}

$installer->endSetup();


