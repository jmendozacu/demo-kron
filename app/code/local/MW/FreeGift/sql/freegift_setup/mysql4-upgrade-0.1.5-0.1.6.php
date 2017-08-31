<?php
$installer = $this;
$installer->startSetup();

try{
    $installer->getConnection()
        ->addColumn($installer->getTable('freegift/salesrule'),
            'coupon_type',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable' => false,
                'length'   => 6,
                'default' => '0',
                'comment' => 'coupon_type conditions'
            )
        );

    $installer->getConnection()
        ->addColumn($installer->getTable('freegift/salesrule'),
            'use_auto_generation',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable' => false,
                'length'   => 6,
                'default' => '0',
                'comment' => 'use_auto_generation conditions'
            )
        );

    $installer->getConnection()
        ->addColumn($installer->getTable('freegift/salesrule'),
            'promotion_banner',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'comment' => 'promotion_banner conditions'
            )
        );
    $installer->getConnection()
        ->addColumn($installer->getTable('freegift/salesrule'),
            'promotion_message',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => false,
                'default' => '',
                'comment' => 'promotion_message conditions'
            )
        );

    $installer->getConnection()
        ->addColumn($installer->getTable('freegift/salesrule'),
            'number_of_free_gift',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable' => false,
                'default' => '1',
                'length'   => 10,
                'comment' => 'number_of_free_gift'
            )
        );

    if(!Mage::helper('freegift/sql')->checkTableStatus('mw_freegift_coupon')){
        $table = $installer->getConnection()
            ->newTable($installer->getTable('freegift/coupon'))
            ->addColumn('coupon_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
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
            ->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
                'nullable'  => false,
                'length' => 255,
            ), 'code')
            ->addColumn('usage_limit', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable'  => false,
                'length' => 15,
            ), 'usage_limit')
            ->addColumn('usage_per_customer', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable'  => false,
                'length' => 15,
            ), 'usage_per_customer')
            ->addColumn('times_used', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable'  => false,
                'length' => 15,
            ), 'times_used')
            ->addColumn('expiration_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
                'nullable'  => false,
            ), 'expiration_date')
            ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
                'nullable'  => false,
            ), 'created_at')
            ->addColumn('type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                'nullable'  => false,
                'length' => 6,
            ), 'type')
        ;
        $installer->getConnection()->createTable($table);
    }

}catch (Exception $e){}


$installer->endSetup();