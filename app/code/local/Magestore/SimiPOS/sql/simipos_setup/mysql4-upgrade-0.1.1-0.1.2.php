<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/**
 * database for simipos location
 */
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('simipos_location')};

CREATE TABLE {$this->getTable('simipos_location')} (
    `location_id` int(10) unsigned NOT NULL auto_increment,
    `name` varchar(255) NULL,
    `address` varchar(255) NULL,
    PRIMARY KEY (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE {$this->getTable('simipos_user')}
    ADD COLUMN `location_id` int(10) unsigned NULL;

ALTER TABLE {$this->getTable('sales/order')}
    ADD COLUMN `location_id` int(10) unsigned NULL;

ALTER TABLE {$this->getTable('sales/order_grid')}
    ADD COLUMN `location_id` int(10) unsigned NULL;

");

/**
 * add Bar-Code attribute for Products
 */
$setup = new Mage_Eav_Model_Entity_Setup('catalog_setup');
$attr = array(
    'group'     => 'General',
    'type'      => 'text',
    'input'     => 'text',
    'label'     => 'Barcode',
    'backend'   => '',
    'frontend'  => '',
    'source'    => '',
    'visible'   => 1,
    'user_defined'          => 1,
    'used_for_price_rules'  => 1,
    'position'              => 2,
    'unique'                => 0,
    'default'               => '',
    'sort_order'            => 101,
);
$setup->addAttribute('catalog_product','simipos_barcode',$attr);
Mage::getModel('catalog/resource_eav_attribute')
    ->load($setup->getAttributeId('catalog_product','simipos_barcode'))
    ->addData(array(
	    'is_global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
	    'is_required'   => 0,
	    'is_configurable'   => 1,
	    'is_searchable'     => 1,
	    'is_visible_in_advanced_search' => 0,
	    'is_comparable'     => 0,
	    'is_filterable'     => 0,
	    'is_filterable_in_search'   => 0,
	    'is_used_for_promo_rules'   => 1,
	    'is_html_allowed_on_front'  => 0,
	    'is_visible_on_front'       => 0,
	    'used_in_product_listing'   => 1,
	    'used_for_sort_by'          => 0,
	    'backend_type'              => 'text',
	))->save();

/**
 * add Import/Export barcode profiles
 */
try {
	Mage::getModel('simipos/profile')->setData(array(
	    'name'          => '[SimiPOS]Import Barcode',
	    'entity_type'   => 'product',
	    'store_id'      => '0',
	    'data_transfer' => 'interactive',
	    'direction'     => 'import',
	    'gui_data'      => array(
	        'import'    => array(
	            'number_of_records' => '10',
	            'decimal_separator' => '.'
	        ),
	        'parse'     => array(
	            'type'      => 'csv',
	            'delimiter' => ',',
	            'enclose'   => '"',
	            'fieldnames'=> ''
	        ),
	        'map'       => array(
	            'only_specified'    => "",
	            'product'           => array(
	                'db'    => array('0', 'sku', 'simipos_barcode'),
	                'file'  => array('', 'sku', 'simipos_barcode')
	            )
	        ),
	    ),
	))->save();
} catch (Exception $e) {}
try {
	Mage::getModel('simipos/profile')->setData(array(
	    'name'          => '[SimiPOS]Export Barcode',
	    'entity_type'   => 'product',
	    'direction'     => 'export',
	    'store_id'      => '0',
	    'data_transfer' => 'file',
	    'gui_data'      => 'a:4:{s:6:"export";a:1:{s:13:"add_url_field";s:1:"0";}s:4:"file";a:3:{s:4:"type";s:4:"file";s:8:"filename";s:26:"simipos_export_barcode.csv";s:4:"path";s:10:"var/export";}s:5:"parse";a:4:{s:4:"type";s:3:"csv";s:9:"delimiter";s:1:",";s:7:"enclose";s:1:""";s:10:"fieldnames";s:0:"";}s:3:"map";a:2:{s:14:"only_specified";s:4:"true";s:7:"product";a:2:{s:2:"db";a:2:{i:1;s:3:"sku";i:2;s:15:"simipos_barcode";}s:4:"file";a:2:{i:1;s:3:"sku";i:2;s:15:"simipos_barcode";}}}}',
	    'actions_xml'   => '<action type="catalog/convert_adapter_product" method="load">
    <var name="store"><![CDATA[0]]></var>
</action>
<action type="catalog/convert_parser_product" method="unparse">
    <var name="store"><![CDATA[0]]></var>
    <var name="url_field"><![CDATA[0]]></var>
</action>
<action type="dataflow/convert_mapper_column" method="map">
    <var name="map">
        <map name="sku"><![CDATA[sku]]></map>
        <map name="simipos_barcode"><![CDATA[simipos_barcode]]></map>
    </var>
    <var name="_only_specified">true</var>
</action>
<action type="dataflow/convert_parser_csv" method="unparse">
    <var name="delimiter"><![CDATA[,]]></var>
    <var name="enclose"><![CDATA["]]></var>
    <var name="fieldnames"></var>
</action>
<action type="dataflow/convert_adapter_io" method="save">
    <var name="type">file</var>
    <var name="path">var/export</var>
    <var name="filename"><![CDATA[simipos_export_barcode.csv]]></var>
</action>',
	))->save();
} catch (Exception $e) {}

$installer->endSetup();
