<?php
/**
 * @package    Magedevgroup_Update
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->removeAttribute('catalog_product', 'reward_points');
$installer->removeAttribute('catalog_product', 'reward_no_discount');


$installer->removeAttribute('customer', 'rewardpoints_accumulated');
$installer->removeAttribute('customer', 'rewardpoints_available');
$installer->removeAttribute('customer', 'rewardpoints_spent');
$installer->removeAttribute('customer', 'rewardpoints_lost');
$installer->removeAttribute('customer', 'rewardpoints_waiting');

$installer->removeAttributeGroup('catalog_product', 4, 18);
$installer->removeAttributeGroup('customer', 10, 24);

$installer->endSetup();