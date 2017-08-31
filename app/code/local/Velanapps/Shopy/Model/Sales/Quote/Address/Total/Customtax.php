<?php
class Velanapps_Shopy_Model_Sales_Quote_Address_Total_Customtax extends Mage_Sales_Model_Quote_Address_Total_Abstract{

	protected $status = false;
	
    public function __construct()
    {
        $this->setCode('sales_tax');
    }
 
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
 
        $this->_setAmount(0);
        $this->_setBaseAmount(0);
 
        $quote = $address->getQuote();
		$euCountry = Mage::helper('velan_shopy')->getEuropeanCountry( $address->getCountryId() );
		if($euCountry){
			$resultTax = 0;
			$this->status = true;
		}
		elseif(!$address->getCountryId()){
			$resultTax = 0;
		}
		elseif(!$this->status){
			$taxReduction = $address->getSubtotal() / 1.2;
			$resultTax = $address->getSubtotal()- $taxReduction;
		}
		$quote->setSalesTax(-$resultTax);
		$quote->setBaseSalesTax(-$resultTax);
		$quote->setGrandTotal($quote->getGrandTotal() + $quote->getSalesTax());
		$quote->setBaseGrandTotal($quote->getBaseGrandTotal() + $quote->getBaseSalesTax()); 
	   
		$address->setSalesTax(-$resultTax);
		$address->setBaseSalesTax(-$resultTax);
		$address->setGrandTotal($address->getGrandTotal() + $address->getSalesTax());
		$address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getBaseSalesTax());    
    }
 
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
		if (((float)$address->getSalesTax()) != 0) {
			$address->addTotal(array(
					'code' => $this->getCode(),
					'title' => Mage::helper('sales')->__('Vat Reduction'),
					'value' => $address->getSalesTax(),
					'base_value' => $address->getSalesTax()       
			));
		}
        return $this;
    }
}