<?php
$installer = $this;
$installer->startSetup();



/** Remove existing atribute */
$installer->removeAttribute('catalog_product', 'pay4leter_enable');
$installer->removeAttribute('catalog_product', 'pay4leter_plans');

$installer->addAttribute('catalog_product', "pay4leter_enable", array(
    'type'       => 'int',
    'input'      => 'select',
    'label'      => 'Pay4later Enable',
    'sort_order' => 100,
    'group'      =>'Pay4later Plans',
    'required'   => true,
    'frontend'   => '',
    'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'backend'    => 'eav/entity_attribute_backend_array',
    'option'     => array (
        'values' => array(
            0 => 'No',
            1 => 'Yes'
        )
    ),

));


$installer->addAttribute(
    'catalog_product',
    'pay4leter_plans',
        array(
            'label'             => 'Pay4later plans',
            'group'             =>'Pay4later Plans',
            'type'              => 'text',
            'input'             => 'multiselect',
            'backend'           => 'eav/entity_attribute_backend_array',
            'frontend'          => '',
            'source'            => '',
            'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'visible'           => true,
            'required'          => false,
            'user_defined'      => false,
            'searchable'        => false,
            'filterable'        => false,
            'comparable'        => false,
            'option'            => array (
                                        'values' => array(
                                                '1' => 'ONIF6',
                                                '2' => 'ONIF10',
                                                '3' => 'ONIF12',
                                                '4' => 'ONIF18',
                                                '5' => 'ONIF24',
                                                '6' => 'ONIF36',
                                                '7' => 'ONIF12-12.9',
                                                '8' => 'ONIF18-12.9',
                                                '9' => 'ONIF24-12.9',
                                                '10' => 'ONIF36-12.9',
                                                '11' => 'ONIF12-24.9',
                                                '12' => 'ONIF18-24.9',
                                                '13' => 'ONIF24-24.9',
                                                '14' => 'ONIF36-24.9',
                                                ),
                                     ),
        )
);


$table = $installer->getConnection()
    ->newTable($installer->getTable('pay4leterProductPlans'))
    ->addColumn('plan_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'auto_increment' => true,
        ), 'plan_id')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
        'nullable'  => false,
        ), 'product_id')
    ->addColumn('plan_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Plan Name')
    ->addColumn('plan_codes', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        ), 'Plan Codes')
    ->addColumn('enabled', Varien_Db_Ddl_Table::TYPE_INTEGER, 4, array(
        'nullable'  => false,
        ), 'Plan Codes');
$installer->getConnection()->createTable($table);

$installer->endSetup();
?>