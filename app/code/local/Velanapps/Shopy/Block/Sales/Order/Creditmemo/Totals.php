<?php

/**
 * @category    FlyWebdesign
 * @package     FlyWebdesign_PaypalFee
 */
class Velanapps_Shopy_Block_Sales_order_Creditmemo_Totals extends Velanapps_Shopy_Block_Sales_Order_Totals
{
    protected $_creditmemo = null;

    public function getCreditmemo()
    {
        if ($this->_creditmemo === null) {
            if ($this->hasData('creditmemo')) {
                $this->_creditmemo = $this->_getData('creditmemo');
            } elseif (Mage::registry('current_creditmemo')) {
                $this->_creditmemo = Mage::registry('current_creditmemo');
            } elseif ($this->getParentBlock()->getCreditmemo()) {
                $this->_creditmemo = $this->getParentBlock()->getCreditmemo();
            }
        }
        return $this->_creditmemo;
    }

    public function setCreditmemo($creditmemo)
    {
        $this->_creditmemo = $creditmemo;
        return $this;
    }

    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getCreditmemo();
    }

    /**
     * Initialize order totals array
     *
     * @return Mage_Sales_Block_Order_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $this->removeTotal('base_grandtotal');
        return $this;
    }


}
