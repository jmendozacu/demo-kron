<?php
class Velanapps_Shopy_Block_Sales_Order_Totals extends Mage_Sales_Block_Order_Totals
{
    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
    	parent::_initTotals();
    	
        $source = $this->getSource();

        /**
         * Add Custom Tax Field in my account page
         */
		if (((float)$source->getSalesTax()) != 0) {
			$totals = $this->_totals;
			$newTotals = array();
			if (count($totals)>0) {
				foreach ($totals as $index=>$arr) {
					if ($index == "grand_total") {
						$label = $this->__('Vat Reduction');
						$newTotals['sales_tax'] = new Varien_Object(array(
							'code'  => 'sales_tax',
							'value' => $source->getSalesTax(),
							'base_value' => $source->getBaseSalesTax(),
							'label' => $label
						));
					}
					$newTotals[$index] = $arr;
				}
				$this->_totals = $newTotals;
			}
		}
        
        return $this;
    }
}
