<?php
$installer = $this;
 
$installer->startSetup();

$attrCode = 'pricecompare_updated_date';
$attrGroupName = 'Price Comparison';
$attrLabel = 'Last Updated at';
$attrNote = 'Date at which the competitor sites were compared.';

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
