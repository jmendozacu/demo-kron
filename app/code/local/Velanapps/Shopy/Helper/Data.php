<?php
class Velanapps_Shopy_Helper_Data extends Mage_core_Helper_Abstract
{
	public function getEuropeanCountry( $countryId )
	{
		$eu_countries = Mage::getStoreConfig('general/country/eu_countries');
		$eu_countries_array = explode(',',$eu_countries);
		if(in_array($countryId, $eu_countries_array)){
			return true;
		}
		return false;
	}
}