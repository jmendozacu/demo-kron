<?php
class Velanapps_Shopy_Model_Sales_Order_Invoice_Total_Customtax extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
		$invoice->setSalesTax(0);
        $invoice->setBaseSalesTax(0);
		
        $taxAmount = $invoice->getOrder()->getSalesTax();
        $invoice->setSalesTax($taxAmount);
        
        $baseTaxAmount = $invoice->getOrder()->getBaseSalesTax();
        $invoice->setBaseSalesTax($baseTaxAmount);
		
        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getSalesTax());
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getBaseSalesTax());
		
		return $this;
    }
}