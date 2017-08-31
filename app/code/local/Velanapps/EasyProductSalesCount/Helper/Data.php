<?php
class Velanapps_EasyProductSalesCount_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getActivation(){
		return Mage::getStoreConfig('easyproductsalescount_tab/active_group/activation_key');
	}
}
