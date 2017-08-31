<?php
class Velanapps_Shopy_Block_Adminhtml_Sales_Order_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals
{
	protected function _initTotals()
	{
		parent::_initTotals();
		$source = $this->getSource();
		/**
         * Add Custom tax field
         */
		if (((float)$source->getSalesTax()) != 0) {
			$this->addTotalBefore(new Varien_Object(array(
					'code'      => 'sales_tax',
					'value'     => $source->getSalesTax(),
					'base_value'=> $source->getBaseSalesTax(),
					'label'     => $this->helper('velan_shopy')->__('Vat Reduction'),
			), array('shipping', 'tax')));
		}
        return $this;
	}
}