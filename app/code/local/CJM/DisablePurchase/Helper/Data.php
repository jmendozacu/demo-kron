<?php

class CJM_DisablePurchase_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getDisabledText($_product){
		$defaulttext = Mage::getStoreConfig('disable_purchase/general/defaulttext');
		$disabledtext = Mage::getModel('catalog/product')->load($_product->getId())->getDisabledtext(); 
		return $disabledtext ? '<span style="font-weight:bold; color:#C00;">'.$disabledtext.'</span>' : '<span style="font-weight:bold; color:#C00;">'.$defaulttext.'</span>';
	}
}