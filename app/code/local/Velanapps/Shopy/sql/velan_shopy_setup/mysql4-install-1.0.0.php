<?php		

	$installer = $this;
	$installer->startSetup();
	$connection = $installer->getConnection();
	$quoteAddressTable = $installer->getTable('sales/quote_address');
	$connection->addColumn($quoteAddressTable, 'sales_tax',
        array(
            'type'     	=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'comment'   => 'Sales Tax',
			'nullable'  => true,
			'scale'     => 4,
            'precision' => 12,
        )
    );
	$connection->addColumn($quoteAddressTable, 'base_sales_tax',
        array(
            'type'     	=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'comment'   => 'Base Sales Tax',
			'nullable'  => true,
			'scale'     => 4,
            'precision' => 12,
        )
    );
	
	$quoteTable = $installer->getTable('sales/quote');
	$connection->addColumn($quoteTable, 'sales_tax',
        array(
            'type'     	=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'comment'   => 'Sales Tax',
			'nullable'  => true,
			'scale'     => 4,
            'precision' => 12,
        )
    );
	$connection->addColumn($quoteTable, 'base_sales_tax',
        array(
            'type'     	=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'comment'   => 'Base Sales Tax',
			'nullable'  => true,
			'scale'     => 4,
            'precision' => 12,
        )
    );
	
	$orderTable = $installer->getTable('sales/order');
	$connection->addColumn($orderTable, 'sales_tax',
        array(
            'type'     	=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'comment'   => 'Sales Tax',
			'nullable'  => true,
			'scale'     => 4,
            'precision' => 12,
        )
    );
	$connection->addColumn($orderTable, 'base_sales_tax',
        array(
            'type'     	=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'comment'   => 'Base Sales Tax',
			'nullable'  => true,
			'scale'     => 4,
            'precision' => 12,
        )
    );
	$invoiceTable = $installer->getTable('sales/invoice');
	$connection->addColumn($invoiceTable, 'sales_tax',
        array(
            'type'     	=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'comment'   => 'Sales Tax',
			'nullable'  => true,
			'scale'     => 4,
            'precision' => 12,
        )
    );
	$connection->addColumn($invoiceTable, 'base_sales_tax',
        array(
            'type'     	=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'comment'   => 'Base Sales Tax',
			'nullable'  => true,
			'scale'     => 4,
            'precision' => 12,
        )
    );
	
	$creditMemoTable = $installer->getTable('sales/creditmemo');
	$connection->addColumn($creditMemoTable, 'sales_tax',
        array(
            'type'     	=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'comment'   => 'Sales Tax',
			'nullable'  => true,
			'scale'     => 4,
            'precision' => 12,
        )
    );
	$connection->addColumn($creditMemoTable, 'base_sales_tax',
        array(
            'type'     	=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'comment'   => 'Base Sales Tax',
			'nullable'  => true,
			'scale'     => 4,
            'precision' => 12,
        )
    );
	
	$installer->endSetup();