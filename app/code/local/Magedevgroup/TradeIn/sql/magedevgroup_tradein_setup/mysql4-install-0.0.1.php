<?php
/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'magedevgroup_tradein/tradeinproposal_entity'
 */
$baseTableName = $installer->getTable('magedevgroup_tradein/tradeinproposal_entity');

$table = $installer->getConnection()
    ->newTable($installer->getTable('magedevgroup_tradein/tradeinproposal_entity'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        'unsigned'  => true,
        'auto_increment' => true,
    ), 'Entity Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Entity Type Id')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Attribute Set Id')
    ->addColumn('increment_id', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable'  => false,
        'default'   => '',
    ), 'Increment Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Store Id')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Updated At')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
    ), 'Defines Is Entity Active')
    ->addIndex($this->getIdxName($baseTableName, array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($this->getIdxName($baseTableName, array('store_id')),
        array('store_id'))
    ->addForeignKey($this->getFkName($baseTableName, 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $this->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($this->getFkName($baseTableName, 'store_id', 'core/store', 'store_id'),
        'store_id', $this->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('TradeInProposal Entity Main Table');

$installer->getConnection()->createTable($table);


/**
 * Datetime entity table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magedevgroup_tradein/tradeinproposal_entity_datetime'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable'  => false,
    ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'magedevgroup_tradein_tradeinproposal_entity_datetime',
            array('entity_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('magedevgroup_tradein_tradeinproposal_entity_datetime', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('magedevgroup_tradein_tradeinproposal_entity_datetime', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('magedevgroup_tradein_tradeinproposal_post_entity_datetime', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('magedevgroup_tradein_tradeinproposal_entity_datetime', array('entity_id', 'attribute_id', 'value')),
        array('entity_id', 'attribute_id', 'value'))
    ->addForeignKey(
        $installer->getFkName('magedevgroup_tradein_tradeinproposal_entity_datetime', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('magedevgroup_tradein_tradeinproposal_entity_datetime', 'entity_id', 'magedevgroup_tradein/tradeinproposal_entity_datetime', 'entity_id'),
        'entity_id', $installer->getTable('magedevgroup_tradein/tradeinproposal_entity_datetime'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'magedevgroup_tradein_tradeinproposal_entity_datetime',
            'entity_type_id',
            'eav/entity_type',
            'entity_type_id'
        ),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('TradeInProposal Entity Datetime');

$installer->getConnection()->createTable($table);


/**
 * Decimal entity table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magedevgroup_tradein/tradeinproposal_entity_decimal'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
    ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'magedevgroup_tradein/tradeinproposal_entity_decimal',
            array('entity_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_decimal', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_decimal', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_decimal', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_decimal', array('entity_id', 'attribute_id', 'value')),
        array('entity_id', 'attribute_id', 'value'))
    ->addForeignKey($installer->getFkName('magedevgroup_tradein/tradeinproposal_entity_decimal', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('magedevgroup_tradein/tradeinproposal_entity_decimal', 'entity_id', 'customer/entity', 'entity_id'),
        'entity_id', $installer->getTable('magedevgroup_tradein/tradeinproposal_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('magedevgroup_tradein/tradeinproposal_entity_decimal', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('TradeInProposal Entity Decimal');

$installer->getConnection()->createTable($table);


/**
 * Integer entity table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magedevgroup_tradein/tradeinproposal_entity_int'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
    ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'magedevgroup_tradein/tradeinproposal_entity_int',
            array('entity_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_int', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_int', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_int', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_int', array('entity_id', 'attribute_id', 'value')),
        array('entity_id', 'attribute_id', 'value'))
    ->addForeignKey($installer->getFkName('magedevgroup_tradein/tradeinproposal_entity_int', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('magedevgroup_tradein/tradeinproposal_entity_int', 'entity_id', 'customer/entity', 'entity_id'),
        'entity_id', $installer->getTable('magedevgroup_tradein/tradeinproposal_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('magedevgroup_tradein/tradeinproposal_entity_int', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('TradeInProposal Entity Int');

$installer->getConnection()->createTable($table);


/**
 * Text entity table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magedevgroup_tradein/tradeinproposal_entity_text'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 65535, array(
        'nullable'  => false,
    ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'magedevgroup_tradein/tradeinproposal_entity_text',
            array('entity_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_text', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_text', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_text', array('entity_id')),
        array('entity_id'))
    ->addForeignKey($installer->getFkName('magedevgroup_tradein/tradeinproposal_entity_text', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('magedevgroup_tradein/tradeinproposal_entity_text', 'entity_id', 'customer/entity', 'entity_id'),
        'entity_id', $installer->getTable('magedevgroup_tradein/tradeinproposal_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('magedevgroup_tradein/tradeinproposal_entity_text', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('TradeInProposal Entity Text');

$installer->getConnection()->createTable($table);


/**
 * Varchar entity table
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('magedevgroup_tradein/tradeinproposal_entity_varchar'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Value Id')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Entity Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Attribute Id')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'magedevgroup_tradein/tradeinproposal_entity_varchar',
            array('entity_id', 'attribute_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_varchar', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_varchar', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_varchar', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('magedevgroup_tradein/tradeinproposal_entity_varchar', array('entity_id', 'attribute_id', 'value')),
        array('entity_id', 'attribute_id', 'value'))
    ->addForeignKey($installer->getFkName('magedevgroup_tradein/tradeinproposal_entity_varchar', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('magedevgroup_tradein/tradeinproposal_entity_varchar', 'entity_id', 'customer/entity', 'entity_id'),
        'entity_id', $installer->getTable('magedevgroup_tradein/tradeinproposal_entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('magedevgroup_tradein/tradeinproposal_entity_varchar', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('TradeInProposal Entity Varchar');

$installer->getConnection()->createTable($table);


/**
 * Add Entity type
 */
$installer->addEntityType('magedevgroup_tradein_tradeinproposal',Array(
    'entity_model'          =>'magedevgroup_tradein/tradeinproposal',
    'attribute_model'       =>'',
    'table'                 =>'magedevgroup_tradein/tradeinproposal_entity',
    'increment_model'       =>'',
    'increment_per_store'   =>'0'
));


$installer->installEntities();

$installer->endSetup();
