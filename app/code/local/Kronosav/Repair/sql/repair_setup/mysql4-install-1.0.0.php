<?php 
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
		->newTable($installer->getTable('repair/repair'))
		->addColumn('repair_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'identity'=>true,
			'unsigned'=>true,
			'nullable'=>false,
			'primary'=>true,
			),'Repair Id')
		->addColumn('product_name',Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
			'nullable'=>true,
			'default'=>null,
			),'Product Name')
		->addColumn('product_make',Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
			'nullable'=>false,
			'default'=>null,
			),'Product Make')
		->addColumn('product_model',Varien_Db_Ddl_Table::TYPE_TEXT, 200, array(
			'nullable'=>false,
			'default'=>null,
			),'Product Model')
		->addColumn('serial_no',Varien_Db_Ddl_Table::TYPE_INTEGER, 70, array(
			'nullable'=>false,
			'default'=>0,
			),'Serial No')
		->addColumn('received_date',Varien_Db_Ddl_Table::TYPE_DATE, 70, array(
			'nullable'=>false,
			),'Received Date')
		->addColumn('updated_date',Varien_Db_Ddl_Table::TYPE_DATE, 70, array(
			'nullable'=>false,
			),'Updated Date')
		->addColumn('status',Varien_Db_Ddl_Table::TYPE_TEXT, 70, array(
			'nullable'=>false,
			),'Status')
		->addColumn('deposit_amount',Varien_Db_Ddl_Table::TYPE_FLOAT, 70, array(
			'nullable'=>false,
			),'Deposit Amount')
		->addColumn('repair_cost',Varien_Db_Ddl_Table::TYPE_FLOAT, 70, array(
			'nullable'=>false,
			),'Repair Cost')
		->addColumn('repair_time',Varien_Db_Ddl_Table::TYPE_FLOAT, 20, array(
			'nullable'=>false,
			),'Repair Time')
		->addColumn('repair_details',Varien_Db_Ddl_Table::TYPE_TEXT, 250, array(
			'nullable'=>false,
			),'Repair Details')
		->addColumn('diagnostic_desc',Varien_Db_Ddl_Table::TYPE_TEXT, 250, array(
			'nullable'=>false,
			),'Diagnostic Description')
		->addColumn('repair_submission_date',Varien_Db_Ddl_Table::TYPE_DATE, 70, array(
			'nullable'=>true,
			),'Repair Submission Date')
		->addColumn('miscellaneous_details',Varien_Db_Ddl_Table::TYPE_TEXT, 150, array(
			'nullable'=>false,
			),'Miscellaneous Details')
		->addColumn('entity_id',Varien_Db_Ddl_Table::TYPE_INTEGER, 70, array(
			'nullable'=>false,
			),'Entity ID')		
		->addForeignKey($installer->getFkName('repair/repair', 'entity_id', 'customer/entity', 'entity_id'),
			'entity_id', $installer->getTable('customer/entity'), 'entity_id',
			Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
		->setComment('Repair Log');
			$installer->getConnection()->createTable($table);
$installer->endSetup();
			
		

