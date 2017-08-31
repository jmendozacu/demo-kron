<?php
 
class Velanapps_Tutorial11_Model_Carrier_Shippingmethod
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
 
    protected $_code = 'shippingmethod';
 
    /**
     * Enter description here...
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
 
        $freeBoxes = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getFreeShipping() && !$item->getProduct()->isVirtual()) {
                    $freeBoxes+=$item->getQty();
                }
            }
        }
        $this->setFreeBoxes($freeBoxes);
 
        $result = Mage::getModel('shipping/rate_result');
        if ($this->getConfigData('type') == 'O') { // per order
            $shippingPrice = $this->getConfigData('price');
        } elseif ($this->getConfigData('type') == 'I') { // per item
            $shippingPrice = ($request->getPackageQty() * $this->getConfigData('price')) - ($this->getFreeBoxes() * $this->getConfigData('price'));
        } else {
            $shippingPrice = false;
        }
 
        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);
 
        if ($shippingPrice !== false) {
            $method = Mage::getModel('shipping/rate_result_method');
 
            $method->setCarrier('shippingmethod');
            $method->setCarrierTitle($this->getConfigData('title'));
 
            $method->setMethod('shippingmethod');
            $method->setMethodTitle($this->getConfigData('name'));
 
            if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                $shippingPrice = '0.00';
            }
 
 
            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);
 
            $result->append($method);
        }
 
        return $result;
    }
 
    public function getAllowedMethods()
    {
        return array('shippingmethod'=>$this->getConfigData('name'));
    }
 
}
?>