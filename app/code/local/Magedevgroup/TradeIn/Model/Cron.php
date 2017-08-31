<?php

/**
 * @package    Magedevgroup_TradeIn
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_TradeIn_Model_Cron
{
    const ACCEPT = 2;

    //TODO!!!!!!!!!!!!!!
    public function procAcceptProposal()
    {
        /** @var Magedevgroup_TradeIn_Model_Resource_TradeInProposal_Collection $collection */
        $collection = Mage::getModel('magedevgroup_tradein/tradeInProposal')
            ->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('tradein_status', self::ACCEPT);

        $collection->getSelect()->limit(5);

        /** @var Magedevgroup_TradeIn_Model_Email_Sender $emailSender */
        $emailSender = Mage::getModel('magedevgroup_tradein/email_sender');

        foreach ($collection as $proposal) {
            $emailSender->sendCouponMail($proposal);
        }
    }
}
