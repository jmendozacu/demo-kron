<?php
/**
 * @package    Magedevgroup_AdminPayments
 * @author     Magedevgroup <admin@magedevgroup.com>
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_AdminPayments_Model_Observer
{
    /**
     * Enable Payment Methods as per the store config
     * 
     * @param  Varien_Event_Observer $observer
     * @return void
     */
    public function enablePaymentMethods(Varien_Event_Observer $observer)
    {
        $methodInstance = $observer->getEvent();
        $paymentMethod = $methodInstance->getMethodInstance();
        $config = Mage::getStoreConfig('adminpayments/general/active', $paymentMethod->getStore());
        if (!$config) {
            return;
        }
        $result = $observer->getResult();
        $adminPayments = explode(',', $config);
        if (in_array($paymentMethod->getCode(), $adminPayments)
            && false == $result->isAvailable
        ) {
                $result->isAvailable = true;
        }
    }
}