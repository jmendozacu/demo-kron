<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Pdo_Mysql */

$installer->startSetup();

$connection->update($installer->getTable('cms/block'), array(
    'content'           => '<ul class="payment-logos">
      <li><img src="{{media url="theme/logo-paypal.svg"}}" alt="PayPal" title="PayPal" /></li>
      <li><img src="{{media url="theme/logo-visa.svg"}}" alt="Visa" title="Visa" /></li>
      <li><img src="{{skin url="images/Pay4Later.png"}}" alt="Pay4Later" title="Pay4Later" /></li>
      <li><img src="{{media url="theme/logo-amex.svg"}}" alt="American Express" title="American Express" /></li>
      <li><img src="{{media url="theme/logo-mastercard.svg"}}" alt="Mastercard" title="Mastercard" /></li>
      <li><img src="{{media url="theme/logo-maestro.svg"}}" alt="Maestro" title="Maestro" /></li>
</ul>'
),
    array("identifier = ?" => "footer_bottom_right")
);


$installer->endSetup();
