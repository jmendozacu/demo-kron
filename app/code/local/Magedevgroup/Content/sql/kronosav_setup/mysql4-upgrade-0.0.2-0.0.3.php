<?php
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Pdo_Mysql */

$installer->startSetup();

$connection->update($installer->getTable('cms/page'), array(
    'content'           => '{{block type="itactica_orbitslider/view" identifier="home_slider" template="itactica_orbitslider/view.phtml"}}
{{block type="itactica_featuredproducts/view" identifier="home_featured" template="itactica_featuredproducts/view.phtml"}} 
{{block type="cms/block" block_id="homepage_banners"}} 
{{block type="shopbybrand/featuredbrand" template="shopbybrand/featuredbrandhomepagepblock.phtml"}}
{{block type="itactica_featuredproducts/view" identifier="home_sale" template="itactica_featuredproducts/view.phtml"}}'
),
    array("identifier = ?" => "home")
);



$installer->getConnection()->insertMultiple(
    $installer->getTable('admin/permission_block'),
    array(
        array('block_name' => 'shopbybrand/featuredbrand', 'is_allowed' => 1),
    )
);

$installer->endSetup();
