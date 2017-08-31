<?php
$installer = $this;
$connection = $installer->getConnection();
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('freegift/rule'),
        'condition_customized',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'default' => null,
            'comment' => 'Customized conditions'
        )
    );

$installer->endSetup();