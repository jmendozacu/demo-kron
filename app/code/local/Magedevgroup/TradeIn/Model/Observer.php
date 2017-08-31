<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Model_Observer
{
    public function sendShippingEmail($observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();

        /** @var Magedevgroup_TradeIn_Model_Email_Sender $emailSender */
        $emailSender = Mage::getModel('magedevgroup_tradein/email_sender');

        Mage::log('Observer::sendShippingEmail($observer) [39 start]', null, 'tradein_observer.log');//TODO TEST!!!!!!!!!!!!

        if (substr($order->getCouponCode(), 0, 7) == 'TRADEIN') {
            Mage::log('Observer::sendShippingEmail($observer) [42]', null, 'tradein_observer.log');//TODO TEST!!!!!!!!!!!!
            $emailSender->sendShippingEmail($order);
            Mage::log('Observer::sendShippingEmail($observer) [44]', null, 'tradein_observer.log');//TODO TEST!!!!!!!!!!!!
        }
    }
}
