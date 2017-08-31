<?php

class Ebizmarts_SagePaySuite_Block_Sales_Order_Surcharge extends Mage_Core_Block_Template {
    
    public function initTotals() {

        $parent = $this->getParentBlock();
        $amount = Mage::helper('sagepaysuite/surcharge')->getAmount($parent->getOrder()->getId());

        if(!$amount){
        	return $this;
        }

        $total = new Varien_Object(array(
            'code'      => 'surcharge',
            'value'     => $amount,
            'base_value'=> $amount,
            'label'     => $this->helper('sagepaysuite')->__('Credit Card Surcharge'),
        ));
        $parent->addTotal($total);

        return $this;

    }
    
}