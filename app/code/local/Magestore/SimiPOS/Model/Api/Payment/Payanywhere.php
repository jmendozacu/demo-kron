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
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * SimiPOS Checkout Product API Model
 * Use to call api with prefix: checkout_payment
 * Methods:
 *  info
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Api_Payment_Payanywhere
    extends Magestore_SimiPOS_Model_Api_Abstract
{
    /**
     * Retrieve payanywhere payment method info
     * 
     * @return array
     */
    public function apiInfo()
    {
        return Mage::getModel('simipos/method_payanywhere')->getAppMerchantInfo();
    }
}
