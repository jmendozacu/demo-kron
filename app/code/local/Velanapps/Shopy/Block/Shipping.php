<?php
class Velanapps_Shopy_Block_Shipping extends Mage_Core_Block_Template
{
	/* return current product */
	public function getProduct()
	{
		return Mage::registry('current_product');
	}
	
	/* Check current country has shipping price */
    public function isCurrentCountryAllowed()
	{
		$ip = Mage::helper('core/http')->getRemoteAddr();
		$countryCode = Mage::getSingleton('mageworx_geoip/geoip')->getLocation($ip)->getCode();
		$allowedCountries = array('FR', 'DE', 'CA', 'CN', 'US', 'AU', 'IT', 'ES', 'NL', 'PT');
		return (in_array($countryCode, $allowedCountries)) ? true : false;
	}
	
	/* Get product weight range */
	public function getWeightRange()
	{
		$productWeight = $this->getProduct()->getWeight();
		if($productWeight <= 10):
			$weight = 10;
		elseif($productWeight <= 20):
			$weight = 20;
		elseif($productWeight <= 30):
			$weight = 30;
		else:
			$weight = 50;
		endif;
		
		return $weight;
	}
	
	/* Get product shipping price */
	public function getShippingRate()
	{
		echo $this->getProduct()->getPrice();
	}
}
