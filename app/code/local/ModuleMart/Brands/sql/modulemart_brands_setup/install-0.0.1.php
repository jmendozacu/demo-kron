<?php 
 /**
 * ModuleMart_Brands extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Module-Mart License
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.modulemart.com/license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to modules@modulemart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.modulemart.com for more information.
 *
 * @category   ModuleMart
 * @package    ModuleMart_Brands
 * @author-email  modules@modulemart.com
 * @copyright  Copyright 2014 © modulemart.com. All Rights Reserved
 */
$this->startSetup();
$table = $this->getConnection()
	->newTable($this->getTable('brands/brand'))
	->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'identity'  => true,
		'nullable'  => false,
		'primary'   => true,
		), 'Brand ID')
	->addColumn('brand_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable'  => false,
		), 'Name')

	->addColumn('brand_logo', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		), 'Brand Logo')

	->addColumn('featured_brand', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'  => false,
		), 'Featured')
	
	->addColumn('is_on_top', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'  => false,
		), 'Brands on Top')	

	->addColumn('brand_details', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
		'nullable'  => false,
		), 'Details')

	->addColumn('url_key', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		), 'URL key')

	->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		), 'Status')

	->addColumn('meta_title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		), 'Meta title')

	->addColumn('meta_keywords', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
		), 'Meta keywords')

	->addColumn('meta_description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
		), 'Meta description')

	->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'Brand Creation Time')
	->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
		), 'Brand Modification Time')
	->setComment('Brand Table');
$this->getConnection()->createTable($table);

$table = $this->getConnection()
	->newTable($this->getTable('brands/brand_store'))
	->addColumn('brand_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'nullable'  => false,
		'primary'   => true,
		), 'Brand ID')
	->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'primary'   => true,
		), 'Store ID')
	->addIndex($this->getIdxName('brands/brand_store', array('store_id')), array('store_id'))
	->addForeignKey($this->getFkName('brands/brand_store', 'brand_id', 'brands/brand', 'entity_id'), 'brand_id', $this->getTable('brands/brand'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->addForeignKey($this->getFkName('brands/brand_store', 'store_id', 'core/store', 'store_id'), 'store_id', $this->getTable('core/store'), 'store_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->setComment('Brands To Store Linkage Table');
$this->getConnection()->createTable($table);
$table = $this->getConnection()
	->newTable($this->getTable('brands/brand_product'))
	->addColumn('rel_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Category ID')
	->addColumn('brand_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'default'   => '0',
	), 'Brand ID')
	->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'unsigned'  => true,
		'nullable'  => false,
		'default'   => '0',
	), 'Product ID')
	->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
		'nullable'  => false,
		'default'   => '0',
	), 'Position')
	->addIndex($this->getIdxName('brands/brand_product', array('product_id')), array('product_id'))
	->addForeignKey($this->getFkName('brands/brand_product', 'brand_id', 'brands/brand', 'entity_id'), 'brand_id', $this->getTable('brands/brand'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->addForeignKey($this->getFkName('brands/brand_product', 'product_id', 'catalog/product', 'entity_id'),	'product_id', $this->getTable('catalog/product'), 'entity_id', Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
	->setComment('Brand to Product Linkage Table');
$this->getConnection()->createTable($table);
$this->endSetup();