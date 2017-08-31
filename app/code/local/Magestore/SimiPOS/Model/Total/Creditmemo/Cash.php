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
 * SimiPOS Creditmemo Cash Total Model
 * 
 * @category    Magestore
 * @package     Magestore_SimiPOS
 * @author      Magestore Developer
 */
class Magestore_SimiPOS_Model_Total_Creditmemo_Cash extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
		if (!$creditmemo->getInvoice() || !$creditmemo->getInvoice()->getId()) {
			return ;
		}
        $order = $creditmemo->getOrder();
        if ($order->getSimiposCash() < 0.0001 || $order->getGrandTotal() < 0.0001) {
            return ;
        }
		$ratio = $creditmemo->getGrandTotal() / $order->getGrandTotal();
		$cash = $order->getSimiposCash() * $ratio;
		$baseCash = $order->getSimiposBaseCash() * $ratio;
		
		$maxcash = $order->getSimiposCash();
		$maxbaseCash = $order->getSimiposBaseCash();
		foreach ($order->getCreditmemosCollection() as $existedCreditmemo) {
			if ($maxcash > 0.0001) {
				$maxcash -= $existedCreditmemo->getSimiposCash();
				$maxbaseCash -= $existedCreditmemo->getSimiposBaseCash();
			}
		}
		if ($cash > $maxcash) {
			$cash = $maxcash;
			$baseCash = $maxbaseCash;
		}
        if ($cash > 0.0001) {
            $creditmemo->setSimiposCash($cash)
                ->setSimiposBaseCash($baseCash);
            if ($creditmemo->getGrandTotal() <= $cash) {
                $creditmemo->setBaseGrandTotal(0.0)
                    ->setGrandTotal(0.0);
            } else {
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $cash)
                    ->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseCash);
            }
        }
    }
}
