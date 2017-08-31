<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$staticBlock = array(
    'title' => 'Product page custom info',
    'identifier' => 'product_info_custom_text',
    'content' => '<div class="product-info-custom-text">
<p>For more information on any of our products please call us:</p>
<a href="tel:0343 523 6169">0343 523 6169</a>
</div>',
    'is_active' => 1,
    'stores' => array(0),
);

Mage::getModel('cms/block')->setData($staticBlock)->save();

$installer->endSetup();
