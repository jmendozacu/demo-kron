<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @copyright 	Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Madapter Block
 * 
 * @category 	Magestore
 * @package 	Magestore_Madapter
 * @author  	Magestore Developer
 */
class Magestore_Madapter_Block_Madapter extends Mage_Core_Block_Template {

    /**
     * prepare block's layout
     *
     * @return Magestore_Madapter_Block_Madapter
     */
    public function _prepareLayout() {
		
        if ((Mage::getSingleton('core/session')->getSessionSimitCart() == NULL)
                && $this->isMobile()
                && Mage::helper('madapter')->getConfig('link_alert')
        )
            $this->setTemplate('madapter/madapter.phtml');
        return parent::_prepareLayout();
    }

    public function isMobile() {

        if (!function_exists('getallheaders')) {

            function getallheaders() {
                $head = array();
                foreach ($_SERVER as $name => $value) {
                    if (substr($name, 0, 5) == 'HTTP_') {
                        $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                        $head[$name] = $value;
                    } else if ($name == "CONTENT_TYPE") {
                        $head["Content-Type"] = $value;
                    } else if ($name == "CONTENT_LENGTH") {
                        $head["Content-Length"] = $value;
                    }
                }
                return $head;
            }

        }

        $head = getallheaders();
        if (isset($head['Mobile-App']))
            return false;
        if ($_SERVER["HTTP_USER_AGENT"]) {
            $user_agent = $_SERVER["HTTP_USER_AGENT"];
            if (strstr($user_agent, 'iPhone') || strstr($user_agent, 'iPod')
                    || strstr($user_agent, 'iPad')) {
                Mage::getSingleton('core/session')->setSessionSimitCart(1);
                return true;
            }
        }
        return FALSE;
    }

}