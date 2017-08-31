<?php
/**
 * @package    Magedevgroup_Update
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;

$installer->startSetup();

$installer->removeAttribute('catalog_product', 'groupdeal_datetime_from');
$installer->removeAttribute('catalog_product', 'groupdeal_datetime_to');

$installer->endSetup();
