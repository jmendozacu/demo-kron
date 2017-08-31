<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$connection = $installer->getConnection();
/* @var $connection Varien_Db_Adapter_Pdo_Mysql */

$installer->startSetup();

$connection->update($installer->getTable('cms/block'), array(
    'content'           => '<ul>
      <li class="title">INFORMATION</li>
      <li><a href="{{store url=\'aboutus\'}}">About Us</a></li>
<li><a href="{{store url=\'contact-us\'}}">Customer Service</a></li>
<li><a href="{{store url=\'catalog/seo_sitemap/category/\'}}">Site Map</a></li>
<li><a href="{{store url=\'sales/guest/form/\'}}">Orders and Returns</a></li>
<li><a href="{{store url=\'contact-us\'}}">Contacts</a></li>
<li><a href="http://www.reviewcentre.com/Online-Electronic-Shops/Kronos-AV-www-kronosav-com-reviews_1822408">Testimonials</a></li>
<li><a href="https://kronosav.zendesk.com/hc/en-us/categories/201369765-Blog">Blog</a></li>
<li><a href="http://www.kronosav.com/munich-high-end-showreport-2015">Munich High End Show Report 2015</a></li>
</ul>
			
			'
),
    array("identifier = ?" => "footer_main_first_column")
);


$connection->update($installer->getTable('cms/block'), array(
    'content'           => '<ul>
      <li class="title">WHY BUY FROM US</li>
      <li><em class="icon-left-dir theme-color"></em><a href="{{store url=\'pay4later-payments\'}}">Interest Free Credit</a></li>
<li><em class="icon-left-dir theme-color"></em><a href="{{store url=\'why-buy-from-kronos\'}}">Why Kronos?</a></li>
<li><em class="icon-left-dir theme-color"></em><a href="{{store url=\'shipping-delivery-details.html/?___store=default\'}}">Shipping &amp; Returns</a></li>
<li><em class="icon-left-dir theme-color"></em><a href="{{store url=\'secure-shopping\'}}">Secure Shopping</a></li>
<li><em class="icon-left-dir theme-color"></em><a href="{{store url=\'shipping-delivery-details.html/?___store=default\'}}">International Shipping</a></li>
<li><em class="icon-left-dir theme-color"></em><a href="{{store url=\'group-sales\'}}">Group Sales</a></li>
<li><em class="icon-left-dir theme-color"></em><a href="{{store url=\'winners\'}}">Winners</a></li>
<li><em class="icon-left-dir theme-color"></em><a href="{{store url=\'contact-us\'}}">Click &amp; Collect</a></li>
</ul>'
),
    array("identifier = ?" => "footer_main_second_column")
);

$connection->update($installer->getTable('cms/block'), array(
    'content'           => '<ul>
      <li class="title">Contact Us</li>
<li class="contacts-left"><ul>
      <li class="icon-mapmarker"><p><strong>Address:</strong><br>Kronos N.Ireland<br> 8-9 Scotch Street Center, Dungannon<br> BT701AR Co.Tyrone</p></li>
      <li class="icon-mapmarker"><p><strong>Address:</strong><br>Unit3<br> Stanbridge Park<br> Staplefield Lane <br> Staplefield <br> Near Haywards Heath <br> West Sussex <br> RH17 6AS </p></li>
</ul>
</li>
<li class="contacts-right"><ul>
      <li class="icon-phone">0343 523 6169</li>
      <li class="icon-email-2"><a href="mailto:sales@example.com">support@kronosav.com</a></li>      
<li class="icon-clock">
<p><strong>Working Days/Hours:</strong><br /> <strong>Kronos NI </strong> <br /> Mon - Fri / 10:00AM - 5:30PM<br /> <strong>Kronos East Sussex </strong> <br />Tues - Sat / 10AM - 5:30PM</p>
</li>
</ul>
</li>
</ul>'
),
    array("identifier = ?" => "footer_main_fourth_column")
);


$installer->endSetup();
