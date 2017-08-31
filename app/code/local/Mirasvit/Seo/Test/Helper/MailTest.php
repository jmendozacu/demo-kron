<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced SEO Suite
 * @version   1.1.0
 * @revision  572
 * @copyright Copyright (C) 2014 Mirasvit (http://mirasvit.com/)
 */


require_once dirname(__FILE__).'/../helpers/html2text.php';

class Mirasvit_Seo_Helper_MailTest extends EcomDev_PHPUnit_Test_Case
{
    protected $helper;
    protected function getExpectedMail($code) {
        return file_get_contents(dirname(__FILE__)."/MailTest/expected/$code.html");
    }

    protected function setUp()
    {
        parent::setUp();
        $this->helper = Mage::helper('seo/mail');
        $this->helper->emails = array();
    }

    protected function html2txt($text){
        $h2t = new html2text($text);
        $h2t->set_allowed_tags('<a>');
        $text = $h2t->get_text();
        $text = preg_replace('/\s+/', ' ', $text);
        $text = str_replace(Mage::getUrl(), 'http://example.com/', $text);
        return trim($text);
    }


}