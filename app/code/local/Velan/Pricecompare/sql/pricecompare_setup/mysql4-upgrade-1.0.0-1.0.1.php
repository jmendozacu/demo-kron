<?php
$installer = $this;
 
$installer->startSetup();

$attrCode = 'juno_co_uk_proprice';
$attrGroupName = 'Price Comparison';
$attrLabel = 'juno.co.uk Product Price';
$attrNote = 'Product price in http://www.juno.co.uk/';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
			'group' => $attrGroupName,
			'sort_order' => 1,
			'type' => 'varchar',
			'backend' => '',
			'frontend' => '',
			'label' => $attrLabel,
			'note' => $attrNote,
			'input' => 'text',
			'class' => '',
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

$attrCode = 'superfi_co_uk_proprice';
$attrGroupName = 'Price Comparison';
$attrLabel = 'superfi.co.uk Product Price';
$attrNote = 'Product price in http://www.superfi.co.uk/';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
			'group' => $attrGroupName,
			'sort_order' => 3,
			'type' => 'varchar',
			'backend' => '',
			'frontend' => '',
			'label' => $attrLabel,
			'note' => $attrNote,
			'input' => 'text',
			'class' => '',
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

$attrCode = 'exceptional_av_co_uk_proprice';
$attrGroupName = 'Price Comparison';
$attrLabel = 'exceptional-av.co.uk Product Price';
$attrNote = 'Product price in http://exceptional-av.co.uk/';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
			'group' => $attrGroupName,
			'sort_order' => 5,
			'type' => 'varchar',
			'backend' => '',
			'frontend' => '',
			'label' => $attrLabel,
			'note' => $attrNote,
			'input' => 'text',
			'class' => '',
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

$attrCode = 'audioaffairco_uk_proprice';
$attrGroupName = 'Price Comparison';
$attrLabel = 'audioaffair.co.uk Product Price';
$attrNote = 'Product price in http://audioaffair.co.uk/';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
			'group' => $attrGroupName,
			'sort_order' => 7,
			'type' => 'varchar',
			'backend' => '',
			'frontend' => '',
			'label' => $attrLabel,
			'note' => $attrNote,
			'input' => 'text',
			'class' => '',
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

$attrCode = 'audiot_co_uk_proprice';
$attrGroupName = 'Price Comparison';
$attrLabel = 'audiot.co.uk Product Price';
$attrNote = 'Product price in http://audiot.co.uk/';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
			'group' => $attrGroupName,
			'sort_order' => 9,
			'type' => 'varchar',
			'backend' => '',
			'frontend' => '',
			'label' => $attrLabel,
			'note' => $attrNote,
			'input' => 'text',
			'class' => '',
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

$attrCode = 'richersounds_com_proprice';
$attrGroupName = 'Price Comparison';
$attrLabel = 'richersounds.com Product Price';
$attrNote = 'Product price in http://richersounds.com/';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
			'group' => $attrGroupName,
			'sort_order' => 11,
			'type' => 'varchar',
			'backend' => '',
			'frontend' => '',
			'label' => $attrLabel,
			'note' => $attrNote,
			'input' => 'text',
			'class' => '',
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

$attrCode = 'sevenoakssav_co_uk_proprice';
$attrGroupName = 'Price Comparison';
$attrLabel = 'sevenoakssoundandvision.co.uk Product Price';
$attrNote = 'Product price in http://sevenoakssoundandvision.co.uk/';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
	$objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
			'group' => $attrGroupName,
			'sort_order' => 13,
			'type' => 'varchar',
			'backend' => '',
			'frontend' => '',
			'label' => $attrLabel,
			'note' => $attrNote,
			'input' => 'text',
			'class' => '',
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

$installer->endSetup();

