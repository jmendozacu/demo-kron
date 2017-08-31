<?php
/**
 * @package    Magedevgroup_Update
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

$installer = $this;
$installer->startSetup();
/*
 * @TODO Fix this upgrade
$installer->removeAttribute('quote', 'rewardpoints_description');
$installer->removeAttribute('quote', 'rewardpoints_quantity');
$installer->removeAttribute('quote', 'base_rewardpoints');
$installer->removeAttribute('quote', 'rewardpoints');
$installer->removeAttribute('quote', 'rewardpoints_referrer');

$installer->removeAttribute('order', 'rewardpoints_description');
$installer->removeAttribute('order', 'rewardpoints_quantity');
$installer->removeAttribute('order', 'base_rewardpoints');
$installer->removeAttribute('order', 'rewardpoints');
$installer->removeAttribute('order', 'rewardpoints_referrer');
*/
$installer->endSetup();