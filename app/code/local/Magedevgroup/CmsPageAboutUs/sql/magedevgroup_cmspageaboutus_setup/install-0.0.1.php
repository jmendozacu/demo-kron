<?php
/**
 * @package    Magedevgroup_CmsPageAboutUs
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$content = <<<EOF
   <div class="cms-page">
    <div class="about-us">
        <section class="sec01">
            <div class="paralax-1">
                <div class="new-paralax">
                    <div class="overlay"></div>
                    <div class="content">
                    <h1>OUR STORY</h1>
                        <p class="text">
                            Kronos was founded in 1991 originally as a HiFi distribution company by David Campbell.
                            Kronos Distribution became the first company to import highly regarded brands such as Triangle Loudspeakers,
                            Audiomeca Turntables & CD Players, YBA Electronics, Copenhagen Transformers (Mains Conditioners)  and became the original
                            importer of Pro-Ject Audio Turntables into the UK.
                            During that time we won a number of prestigious awards including What Hi Fi product of the year for the Pro-Ject 6 Turntable.
                    </p>
                </div>
            </div>
        </section>
        <section class="sec02">
            <div class="paralax-1">
                <div class="new-paralax">
                    <div class="overlay"></div>
                    <div class="content">
                        <h1>1996</h1>
                        <p class="text">
                            In 1996 Kronos Distribution moved in retail and became known as Kronos HIFI.
                            We opened our first retail outlet in Dungannon, N.Ireland, and began to expand our range of
                            services by creating our Home Cinema department. Alongside our new Home Cinema range Kronos HIFI
                            brought the largest range of specialist HIFI products to N.Ireland and to this ay we continue to have
                            one of the largest range of HIFI products in the UK!
                        </p>
                    </div>
                </div>
        </section>
        </section>
        <section class="sec03">
            <div class="paralax-1">
                <div class="new-paralax">
                    <div class="overlay"></div>
                    <div class="content">
                        <h1>1999</h1>
                        <p class="text">
                            Kronos HIFI continued to grow  and in late 1999 we moved into our new larger premises,
                            which featured a 1000sq.ft showroom, and the building hosted separate demonstration rooms
                            for HIFI and Home Cinema systems. Due to demand, Kronos HIFI expanded into custom installation,
                            multi room audio systems and projection systems, and became one of the first dealers to become an
                            approved CEDIA (Custom Electronic Design and installation Association) installer.
                            Specialist HiFi products continued to be at the forefront of the business.
                        </p>
                    </div>
                </div>
        </section>
        </section>
        <section class="sec04">
            <div class="paralax-1">
                <div class="new-paralax">
                    <div class="overlay"></div>
                    <div class="content">
                        <h1>2001</h1>
                        <p class="text">
                            In 200, Kronos HIFI evolved into Kronos Audio Visual, as we felt this reflected
                            the company’s diversification into both the audio and video market place.
                            Kronos Audio Visual shortly after joined the online marketplace and quickly asserted
                            itself as one of the UK’s leading e-commerce stores for specialist HiFi & Home Cinema products.
                            In 2014, we completely re designed our showroom, demonstration rooms and office space.
                            E-commerce has become a huge part of Kronos AV. Part of our re design was
                            to incorporate dedicated shipping areas, which included a dedicated packaging etc...
                        </p>
                    </div>
                </div>
        </section>
        </section>
        <section class="sec05">
            <div class="paralax-1">
                <div class="new-paralax">
                    <div class="overlay"></div>
                    <div class="content">
                        <h1>2015</h1>
                        <p class="text">
                            In 2015 we opened our first store on the UK mainland. Having grown our reputation across N.Ireland and becoming
                            a well known online retailer across the UK,
                            it seemed like the right time to give our UK customers a store to
                            visit and meet the team. Our first store opened in Uckfield, East Sussex.
                            A key factor for us was giving our UK customers a store where they could hear our stunning range of products.
                           <span class="padding"></span>
                            When we decided too make the jump to the UK mainland,
                            we knew we would need someone who could manage our store,
                            offer our customers he same level of knowledge they have come to expect
                            and deliver a fantastic service to all of our customers. That is where Richard Allan came in.
                            Richard has over 30 years experience in the HiFi industry, and has an array of knowledge across most brands.
                            Richard has been heavily involved in product design, and later retail, so is a wealth of knowledge,
                            which includes a staggeringly impressive range of technical knowledge of most products.
                        </p>
                    </div>
                </div>
        </section>
        </section>
        <section class="sec06">
            <div class="paralax-1">
                <div class="new-paralax">
                    <div class="overlay"></div>
                    <div class="content">
                        <h1>2016</h1>
                        <p class="text">
                            In 2016,following a successful first year,
                            we moved our store to Staplefeld, West Sussex,
                            as we felt this was a better area and was much more accessible
                            for many of our customers. Richard remained at the realm, and Kronos’s reputation has
                            been growing year on year and we hope to continue to grow this throughout the whole of the UK.
                        </p>
                    </div>
                </div>
        </section>
    </div>
</div>
EOF;

$cmsPage = array(
    'page_id' => 3,
    'title' => 'About Us',
    'root_template' => 'one_column',
    'identifier' => 'aboutus',
    'content' => $content,
    'layout_update_xml' => "",
    'is_active' => 1,
    'stores' => array(1),
    'sort_order' => 0
);

$page = Mage::getModel('cms/page')->load(3);
$page->setData($cmsPage)->save();

$installer->endSetup();
