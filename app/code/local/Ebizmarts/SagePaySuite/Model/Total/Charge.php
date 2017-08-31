<?php

class Ebizmarts_SagePaySuite_Model_Total_Charge extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    public function __construct() {
        $this->setCode('surcharge');
    }

    public function collect(Mage_Sales_Model_Quote_Address $address) {
        parent::collect($address);

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            $this->_setAmount(0);
            $this->_setBaseAmount(0);

            return $this;
        }

        $amount = Mage::helper('sagepaysuite/surcharge')->getChargeAmount($address);
        if ($amount) {
            $this->_setAmount($amount);
            $this->_setBaseAmount($amount);
        }
    }

    /**
     * Add shipping totals information to address object
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Total_Shipping
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address) {
        $amount = $address->getSurchargeAmount();
        if ($amount != 0) {
            $title = Mage::helper('sagepaysuite')->__('Card Surcharge');
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => $title,
                'value' => $address->getSurchargeAmount()
            ));
        }
        return $this;
    }

    /**
     * Get Shipping label
     *
     * @return string
     */
    public function getLabel() {
        return Mage::helper('sagepaysuite')->__('Card Surcharge');
    }

    public function initTotals() {

        return $this; //Return $this, breaks for example Sweet Tooth otherwise

        $this->_totals['surcharge'] = new Varien_Object(array(
                    'code' => 'surcharge',
                    'strong' => true,
                    'value' => $this->getSource()->getTotalDue(),
                    'base_value' => $this->getSource()->getBaseTotalDue(),
                    'label' => $this->helper('sales')->__('ssss Due'),
                    'area' => 'footer'
                ));

        return $this;
    }

}