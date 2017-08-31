<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$staticBlock = array(
    'title' => 'Category page bottom',
    'identifier' => 'category_page_bottom',
    'content' => '<div class="category-page-bottom">
<div class="category-bottom category-bottom-left">
<img src="{{skin url="images/delivery.png"}}" alt="">
<h3>Free Shipping</h3>
<p>on all orders over £50</p>
</div>
<div class="category-bottom category-bottom-center">
<img src="{{skin url="images/gaurantee.png"}}" alt="">
<h3>Guarantee</h3>
<p>14 day money back</p>
</div>
<div class="category-bottom category-bottom-right">
<img src="{{skin url="images/support.png"}}" alt="">
<h3>24/7 Customer Support</h3>
<p>call us on 0343 523 6169</p>
</div>
</div>',
    'is_active' => 1,
    'stores' => array(0),
);

Mage::getModel('cms/block')->setData($staticBlock)->save();

$installer->endSetup();
