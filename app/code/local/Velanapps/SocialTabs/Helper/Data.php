<?php
class Velanapps_SocialTabs_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getStatus() {
		return Mage::getStoreConfig('socialtabs/general/status', $this->getStoreId());
    }
}
