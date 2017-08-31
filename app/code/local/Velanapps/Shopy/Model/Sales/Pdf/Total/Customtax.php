<?php
class Velanapps_Shopy_Model_Sales_Pdf_Total_Customtax extends Mage_Sales_Model_Order_Pdf_Total_Default 
{
	public function getTotalsForDisplay(){
        $amount = $this->getOrder()->getSalesTax();
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
		
        if(((float) $amount) != 0){
            //$amount = $this->getOrder()->formatPrice($amount);
			
			$totals = array(
                array(
                    'label'     => Mage::helper('sales')->__('Vat Reduction:'),
                    'amount'    => $amount,
                    'font_size' => $fontSize,
                )
            );

            return $totals;
        }
    }
}

?>