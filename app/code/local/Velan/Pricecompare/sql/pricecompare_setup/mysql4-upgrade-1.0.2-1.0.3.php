<?php
$installer = $this;
 
$installer->startSetup();

$attrCode = 'pricecompare_minimum';
$attrGroupName = 'Price Comparison';
$attrLabel = 'Minimum Product Price';
$attrNote = 'Enter the minimum price below which our price would not be updated when compared with competitor prices';

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
