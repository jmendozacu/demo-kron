<?php
class Velanapps_Shopy_Model_Sales_Order_Creditmemo_Total_Customtax extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
    	$creditmemo->setSalesTax(0);
        $creditmemo->setBaseSalesTax(0);

        $amount = $creditmemo->getOrder()->getSalesTax();
        $creditmemo->setSalesTax($amount);
        
        $amount = $creditmemo->getOrder()->getBaseSalesTax();
        $creditmemo->setBaseSalesTax($amount);
        
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getSalesTax());
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getBaseSalesTax());

        return $this;
    }
}