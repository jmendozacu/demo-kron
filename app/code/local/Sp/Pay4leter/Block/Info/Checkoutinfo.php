<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * @category   Sp
 * @package    Sp_Pay4later
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Sp_Pay4leter_Block_Info_Checkoutinfo extends Mage_Payment_Block_Info
{
    /**
     * Init default template for block
     */
    protected function _construct()
    {

        parent::_construct();
        $this->setTemplate('pay4leter/checkout_pay4leter.phtml');
    }
}
