<?php
/**
 * @package    Magedevgroup_Update
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;

//Remove attributes
$installer->removeAttribute('catalog_category', 'umm_cat_block_proportions');
$installer->removeAttribute('catalog_category', 'umm_cat_block_top');
$installer->removeAttribute('catalog_category', 'umm_cat_block_bottom');
$installer->removeAttribute('catalog_category', 'umm_cat_block_right');
$installer->removeAttribute('catalog_category', 'umm_cat_label');

$installer->endSetup();