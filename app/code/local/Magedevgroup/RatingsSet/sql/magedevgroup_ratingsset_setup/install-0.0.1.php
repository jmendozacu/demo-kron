<?php
/**
 * @package    Magedevgroup_RatingsSet
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

$this->startSetup();

$table = new Varien_Db_Ddl_Table();
$table->setName($this->getTable('magedevgroup_ratingsset/set'));

$table->addColumn(
    'entity_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    10,
    array(
        'auto_increment' => true,
        'unsigned' => true,
        'nullable'=> false,
        'primary' => true
    )
);

$table->addColumn(
    'name',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable' => false,
    )
);

$table->addColumn(
    'ratings',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    null,
    array(
        'nullable' => false,
    )
);
/**
 * These two important lines are often missed.
 */
$table->setOption('type', 'InnoDB');
$table->setOption('charset', 'utf8');

/**
 * Create the table!
 */
$this->getConnection()->createTable($table);

$this->endSetup();