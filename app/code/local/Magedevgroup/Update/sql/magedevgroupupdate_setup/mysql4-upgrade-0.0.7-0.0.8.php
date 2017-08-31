<?php
/**
 * @package    Magedevgroup_Update
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->removeAttribute('catalog_product', 'rw_video_code');
$installer->removeAttribute('catalog_product', 'rw_video_height');
$installer->removeAttribute('catalog_product', 'rw_video_thumb_height');
$installer->removeAttribute('catalog_product', 'rw_video_thumb_width');
$installer->removeAttribute('catalog_product', 'rw_video_title');
$installer->removeAttribute('catalog_product', 'rw_video_width');

$installer->endSetup();