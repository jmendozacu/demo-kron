<?php

class Magedevgroup_TradeIn_Model_Email_Sender
{
    const COUPSEND = 3;
    const SHIPSEND = 4;
    const XML_PATH_EMAIL_SENDER = 'magedevgroup_tradein/general/sender';
    const XML_PATH_EMAIL_COUPON_TEMPLATE_A = 'magedevgroup_tradein/general/coupon_template_a';
    const XML_PATH_EMAIL_COUPON_TEMPLATE_P = 'magedevgroup_tradein/general/coupon_template_p';
    const XML_PATH_EMAIL_SHIPPING_TEMPLATE = 'magedevgroup_tradein/general/shipping_template';

    public function sendCouponMail(Magedevgroup_TradeIn_Model_TradeInProposal $proposal)
    {
        // Who were sending to...
        $customerEmail = $proposal->getData('mail');
        $customerName = $proposal->getData('fname') . " " . $proposal->getData('sname');

        $couponcode = Mage::getModel('magedevgroup_tradein/rule_priceRule')
            ->createRule(
                $proposal->getData('current_product'),
                $proposal->getData('discount_type'),
                $proposal->getData('discount_amount')
            );

        $proposal->setData('coupon_code', $couponcode);

        // Here is where we can define custom variables to go in our email template!
        $emailTemplateVariables = array(
            'fname' => $proposal->getData('fname'),
            'sname' => $proposal->getData('sname'),
            'product_name' => Mage::getModel('catalog/product')
                ->load($proposal->getData('current_product'))
                ->getName(),
            'product_url' => Mage::getModel('catalog/product')
                ->load($proposal->getData('current_product'))
                ->getProductUrl(),
            'discount_amount' => $proposal->getData('discount_amount'),
            'brand' => $proposal->getData('brand'),
            'model' => $proposal->getData('model'),
            'coupon_code' => $couponcode
        );

        /** @var $mailTemplate Mage_Core_Model_Email_Template */
        $mailTemplate = Mage::getModel('core/email_template');

        if($proposal->getData('discount_type')==0) { //Send e-mail with discount AMOUNT
            $mailTemplate->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_EMAIL_COUPON_TEMPLATE_A),
                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                $customerEmail,
                $customerName,
                $emailTemplateVariables
            );
        }else{//Send e-mail with discount PERCENTAGE
            $mailTemplate->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_EMAIL_COUPON_TEMPLATE_P),
                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                $customerEmail,
                $customerName,
                $emailTemplateVariables
            );
        }

        if ($mailTemplate->getSentSuccess()) {
            $proposal->setData('tradein_status', self::COUPSEND);
            $proposal->save();
        }
    }

    public function sendShippingEmail(Mage_Sales_Model_Order $order)
    {
        // Who were sending to...
        $customerEmail = $order->getCustomerEmail();
        $customerName = $order->getCustomerName();

        // Here is where we can define custom variables to go in our email template!
        $emailTemplateVariables = array(
            'customer_name' => $customerName,
            'order_increment_id' => $order->getRealOrderId(),
        );

        Mage::log('Email_Sender::sendShippingEmail(Mage_Sales_Model_Order $order) [69]', null, 'tradein_observer.log');//TODO TEST!!!!!!!!!!!!

        /** @var $mailTemplate Mage_Core_Model_Email_Template */
        $mailTemplate = Mage::getModel('core/email_template');
        $mailTemplate->sendTransactional(
            Mage::getStoreConfig(self::XML_PATH_EMAIL_SHIPPING_TEMPLATE),
            Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
            $customerEmail,
            $customerName,
            $emailTemplateVariables
        );

        /** @var Magedevgroup_TradeIn_Model_Resource_TradeInProposal_Collection $collection */
        $collection = Mage::getModel('magedevgroup_tradein/tradeInProposal')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('coupon_code', $order->getCouponCode());

        /** @var  Magedevgroup_TradeIn_Model_Resource_TradeInProposal $proposal */
        $proposal = $collection->getFirstItem();

        Mage::log('Email_Sender::sendShippingEmail(Mage_Sales_Model_Order $order) [90]', null, 'tradein_observer.log');//TODO TEST!!!!!!!!!!!!

        if ($mailTemplate->getSentSuccess()) {
            Mage::log('Email_Sender::sendShippingEmail(Mage_Sales_Model_Order $order) [93]', null, 'tradein_observer.log');//TODO TEST!!!!!!!!!!!!
            $proposal->setData('tradein_status', self::SHIPSEND);
            $proposal->save();
            Mage::log('Email_Sender::sendShippingEmail(Mage_Sales_Model_Order $order) [96 final]', null, 'tradein_observer.log');//TODO TEST!!!!!!!!!!!!
        }
    }
}
