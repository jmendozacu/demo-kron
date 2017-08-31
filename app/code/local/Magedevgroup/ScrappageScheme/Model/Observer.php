<?php

/**
 * @package    Magedevgroup_ScrappageScheme
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class  Magedevgroup_ScrappageScheme_Model_Observer
{
    public function proCollection(Varien_Event_Observer $observer)
    {
        //Get the Order ID via Observer
        $orderId = $observer->getEvent()->getOrderIds();

        //Check
        if (!is_array($orderId) || (!array_key_exists(0, $orderId))) {
            return;
        }

        //Collection for Last Real order Datas
        $salesOrder = Mage::getModel('sales/order')->load($orderId[0]);

        //Check Order ID
        if (!$salesOrder->getId()) {
            return;
        }

        //Define order Increment ID
        $orderIncrementId = $salesOrder->getIncrementId();

        //Collection for Last Real Order
        $orderCustomOptions = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $orderCustomOptions = $orderCustomOptions->getItemsCollection();

        foreach ($orderCustomOptions as $productOptions) {
            $options = $productOptions->getProductOptions();

            foreach ($options as $option) {
                foreach ($option as $opt) {
                    //Checking value
                    if (($opt['label'] == 'Trade-In Guarantee' || $opt['label'] == 'Scrappage Scheme Trade In' || $opt['label'] == 'Scrappage Scheme') && ($opt['value'] == "Yes I would like to trade in my old item")) {
                        $this->confirmationEmail($orderIncrementId);
                        return true;
                    }
                }
            }
        }
    }

    //In this function Collect the order data and Send Email to Customers
    public function confirmationEmail($orderIncrementId)
    {
        //Collection for order Data
        $salesOrder = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        //Get Customer Email
        $customerEmail = $salesOrder->getCustomerEmail();

        //Get Customer Name
        $customerName = ucfirst($salesOrder->getCustomerName());

        //Get Email Template
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('scrappage_scheme_email_template');

        //Getting the Store E-Mail Sender Name.
        $senderName = Mage::getStoreConfig('trans_email/ident_sales/name');

        //Getting the Store General E-Mail.
        $senderEmail = Mage::getStoreConfig('trans_email/ident_sales/email');

        //Variables for Twitter Confirmation Mail.
        $emailTemplateVariables = array();

        //Initialized Customer Name
        $emailTemplateVariables['customer_name'] = ucfirst($salesOrder->getCustomerName());

        //Initializd Order Increment ID
        $emailTemplateVariables['order_increment_id'] = $orderIncrementId;

        //Initializd Base URL
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        //Set the variables into the Email Template
        $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);

        //Sending Email to Customers
        $mail = Mage::getModel('core/email')
            ->setToName($customerName)
            ->setToEmail($customerEmail)
            ->setBody($processedTemplate)
            ->setSubject('Trade-In Guarantee Order : #' . $orderIncrementId)
            ->setFromEmail($senderEmail)
            ->setFromName($senderName)
            ->setType('html');

        try {
            //Here Mail send
            $mail->send();
        } catch (Exception $error) {
            //Get Error Messages in log
            Mage::log($error->getMessage(), null, 'Trade-In_Guarantee.log', true);
            return false;
        }
        return true;
    }
}