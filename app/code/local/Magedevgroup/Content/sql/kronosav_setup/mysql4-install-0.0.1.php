<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Pdo_Mysql */

$installer->startSetup();

$staticBlock = array(
    'title' => 'Homepage banners section',
    'identifier' => 'homepage_banners',
    'content' => '<div class="homepage-banners row">
    <div href="#" class="home-banner-item small-12 medium-12 large-6 columns">
<img src="{{media url="wysiwyg/home-banner-1.jpg"}}" alt="" />
        <div class="banner-text banner-text-left">
            <div class="banner-top-title red-banner-title">Hi fi</div>
            <a href="{{config path="web/unsecure/base_url"}}hi-fi.html" class="banner-bottom-subtitle">Shop Now</a>
        </div>
    </div>
    <div href="#" class="home-banner-item small-12 medium-12 large-6 columns">
<img src="{{media url="wysiwyg/home-banner-2.jpg"}}" alt="" />
        <div class="banner-text banner-text-right">
            <div class="banner-top-title black-banner-title">Best of British</div>
            <a href="{{config path="web/unsecure/base_url"}}best-of-british.html" class="banner-bottom-subtitle">Shop Now</a>
        </div>
    </div>
    <div href="#" class="home-banner-item small-12 medium-12 large-4 columns">
<img src="{{media url="wysiwyg/home-banner-3.jpg"}}" alt="" />
        <div class="banner-text banner-text-left">
            <div class="banner-top-title black-banner-title">Streaming</div>
            <a href="{{config path="web/unsecure/base_url"}}streaming.html" class="banner-bottom-subtitle">Shop Now</a>
        </div>
    </div>
    <div href="#" class="home-banner-item small-12 medium-12 large-4 columns">
<img src="{{media url="wysiwyg/home-banner-4.jpg"}}" alt="" />
        <div class="banner-text banner-text-center">
            <div class="banner-top-title"><div>Sale</div>now on</div>
            <a href="{{config path="web/unsecure/base_url"}}sale-40.html" class="banner-bottom-subtitle red-banner-title">Shop Sale</a>
        </div>
    </div>
    <div href="#" class="home-banner-item small-12 medium-12 large-4 columns">
<img src="{{media url="wysiwyg/home-banner-5.jpg"}}" alt="" />
        <div class="banner-text banner-text-right">
            <div class="banner-top-title red-banner-title">Home cinema</div>
            <a href="{{config path="web/unsecure/base_url"}}home-cinema.html" class="banner-bottom-subtitle">Shop Now</a>
        </div>
    </div>
</div>',
    'is_active' => 1,
    'stores' => array(0),
);

$connection->update($installer->getTable('cms/block'), array(
    'content'           => '<h5>Follow us on<h5>'
),
    array("identifier = ?" => "footer_top_left")
);

$connection->update($installer->getTable('cms/block'), array(
    'content'           => '<h5>Follow us on<h5>'
),
    array("identifier = ?" => "footer_top_left")
);


$installer->endSetup();
