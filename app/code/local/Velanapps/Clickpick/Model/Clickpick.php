<?php

class Velanapps_Clickpick_Model_Clickpick extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
  protected $_code = 'velanapps_clickpick_dungannon';
 
  public function collectRates(Mage_Shipping_Model_Rate_Request $request)
  {
    $result = Mage::getModel('shipping/rate_result');
    $result->append($this->_getDefaultRate());
    $result->append($this->_getUckfieldShippingRate());
 
    return $result;
  }
 
  public function getAllowedMethods()
  {
    return array(
      	'velanapps_clickpick_dungannon' => $this->getConfigData('name'),
    	'velanapps_clickpick_uckfield' => $this->getConfigData('name')
    );
  }
 
  protected function _getDefaultRate()
  {
    $rate = Mage::getModel('shipping/rate_result_method');
     
    $rate->setCarrier($this->_code);
    $rate->setCarrierTitle($this->getConfigData('title'));
    $rate->setMethod($this->_code);
    $rate->setMethodTitle("Store Pick Up (Dungannon, N.Ireland)");
    $rate->setPrice($this->getConfigData('price'));
    $rate->setCost(0);
     
    return $rate;
  }
  
  protected function _getUckfieldShippingRate()
  {
  	$rate = Mage::getModel('shipping/rate_result_method');
  	/* @var $rate Mage_Shipping_Model_Rate_Result_Method */
  	$rate->setCarrier($this->_code);
  	$rate->setCarrierTitle($this->getConfigData('title'));
  	$rate->setMethod('velanapps_clickpick_uckfield');
  	$rate->setMethodTitle('Store Pick Up (Staplefield, West Sussex)');
  	$rate->setPrice(0.00);
  	$rate->setCost(0);
  	return $rate;
  }
  
}