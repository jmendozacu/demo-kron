<?php
$installer = $this;
 
$installer->startSetup();

$attrCode = 'priceurl_juno_co_uk';
$attrGroupName = 'Price Comparison';
$attrLabel = 'juno.co.uk URL';
$attrNote = 'Enter the product url in http://www.juno.co.uk/';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
			'group' => $attrGroupName,
			'sort_order' => 0,
			'type' => 'varchar',
			'backend' => '',
			'frontend' => '',
			'label' => $attrLabel,
			'note' => $attrNote,
			'input' => 'text',
			'class' => 'validate-url',
			'source' => '',
			'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			'visible' => true,
			'required' => false,
			'user_defined' => true,
			'default' => '0',
			'visible_on_front' => false,
			'unique' => false,
			'is_configurable' => false,
			'used_for_promo_rules' => true
	));
}

$attrCode = 'priceurl_superfi_co_uk';
$attrGroupName = 'Price Comparison';
$attrLabel = 'superfi.co.uk URL';
$attrNote = 'Enter the product url in http://www.superfi.co.uk/';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
			'group' => $attrGroupName,
			'sort_order' => 2,
			'type' => 'varchar',
			'backend' => '',
			'frontend' => '',
			'label' => $attrLabel,
			'note' => $attrNote,
			'input' => 'text',
			'class' => 'validate-url',
			'source' => '',
			'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			'visible' => true,
			'required' => false,
			'user_defined' => true,
			'default' => '0',
			'visible_on_front' => false,
			'unique' => false,
			'is_configurable' => false,
			'used_for_promo_rules' => true
	));
}

$attrCode = 'priceurl_exceptional_av_co_uk';
$attrGroupName = 'Price Comparison';
$attrLabel = 'exceptional-av.co.uk URL';
$attrNote = 'Enter the product url in http://exceptional-av.co.uk/';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
			'group' => $attrGroupName,
			'sort_order' => 4,
			'type' => 'varchar',
			'backend' => '',
			'frontend' => '',
			'label' => $attrLabel,
			'note' => $attrNote,
			'input' => 'text',
			'class' => 'validate-url',
			'source' => '',
			'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			'visible' => true,
			'required' => false,
			'user_defined' => true,
			'default' => '0',
			'visible_on_front' => false,
			'unique' => false,
			'is_configurable' => false,
			'used_for_promo_rules' => true
	));
}

$attrCode = 'priceurl_audioaffairco_uk';
$attrGroupName = 'Price Comparison';
$attrLabel = 'audioaffair.co.uk URL';
$attrNote = 'Enter the product url in http://audioaffair.co.uk/';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
			'group' => $attrGroupName,
			'sort_order' => 6,
			'type' => 'varchar',
			'backend' => '',
			'frontend' => '',
			'label' => $attrLabel,
			'note' => $attrNote,
			'input' => 'text',
			'class' => 'validate-url',
			'source' => '',
			'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			'visible' => true,
			'required' => false,
			'user_defined' => true,
			'default' => '0',
			'visible_on_front' => false,
			'unique' => false,
			'is_configurable' => false,
			'used_for_promo_rules' => true
	));
}

$attrCode = 'priceurl_audiot_co_uk';
$attrGroupName = 'Price Comparison';
$attrLabel = 'audioat.co.uk URL';
$attrNote = 'Enter the product url in http://audiot.co.uk/';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
			'group' => $attrGroupName,
			'sort_order' => 8,
			'type' => 'varchar',
			'backend' => '',
			'frontend' => '',
			'label' => $attrLabel,
			'note' => $attrNote,
			'input' => 'text',
			'class' => 'validate-url',
			'source' => '',
			'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			'visible' => true,
			'required' => false,
			'user_defined' => true,
			'default' => '0',
			'visible_on_front' => false,
			'unique' => false,
			'is_configurable' => false,
			'used_for_promo_rules' => true
	));
}

$attrCode = 'priceurl_richersounds_com';
$attrGroupName = 'Price Comparison';
$attrLabel = 'richersounds.com URL';
$attrNote = 'Enter the product url in http://richersounds.com/';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
			'group' => $attrGroupName,
			'sort_order' => 10,
			'type' => 'varchar',
			'backend' => '',
			'frontend' => '',
			'label' => $attrLabel,
			'note' => $attrNote,
			'input' => 'text',
			'class' => 'validate-url',
			'source' => '',
			'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			'visible' => true,
			'required' => false,
			'user_defined' => true,
			'default' => '0',
			'visible_on_front' => false,
			'unique' => false,
			'is_configurable' => false,
			'used_for_promo_rules' => true
	));
}

$attrCode = 'priceurl_sevenoakssav_co_uk';
$attrGroupName = 'Price Comparison';
$attrLabel = 'sevenoakssav.co.uk URL';
$attrNote = 'Enter the product url in http://sevenoakssav.co.uk/';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
			'group' => $attrGroupName,
			'sort_order' => 12,
			'type' => 'varchar',
			'backend' => '',
			'frontend' => '',
			'label' => $attrLabel,
			'note' => $attrNote,
			'input' => 'text',
			'class' => 'validate-url',
			'source' => '',
			'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
			'visible' => true,
			'required' => false,
			'user_defined' => true,
			'default' => '0',
			'visible_on_front' => false,
			'unique' => false,
			'is_configurable' => false,
			'used_for_promo_rules' => true
	));
}

$installer->run("
	
	-- DROP TABLE IF EXISTS {$this->getTable('pricecompare_product_update')};
	
	CREATE TABLE {$this->getTable('pricecompare_product_update')} (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `last_updated_count` int(11) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
	
	INSERT INTO `pricecompare_product_update` (`id`, `last_updated_count`) VALUES (1, 0);
	
");

$installer->endSetup();