<?php

require_once 'app/Mage.php';
Mage::app();


/** @var Mage_Catalog_Model_Product $product */
$product = Mage::getModel('catalog/product')->load(5817);

echo $product->getName()."  ".$product->getFbProduct();


$layout = <<<EOF
<reference name="head">
    <action method="addJs"><script>change/change-class.js</script></action>
    <action method="addCss"><stylesheet>css/conversion.css</stylesheet></action>
</reference>
EOF;

var_dump($layout);
