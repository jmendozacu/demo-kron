<?php
class MW_FreeGift_Model_Rule_Condition_Product_Found
extends Mage_SalesRule_Model_Rule_Condition_Product_Found
{
	public function __construct()
	{
		parent::__construct();
		$this->setType('salesrule/rule_condition_product_found');
	}
	
	public function loadValueOptions()
	{
		$this->setValueOption(array(
			1 => Mage::helper('salesrule')->__('FOUND'),
			0 => Mage::helper('salesrule')->__('NOT FOUND')
		));
		return $this;
	}
	
	public function asHtml()
	{
		$html = $this->getTypeElement()->getHtml() . Mage::helper('salesrule')->__("If an item is %s in the cart with %s of these conditions true:", $this->getValueElement()->getHtml(), $this->getAggregatorElement()->getHtml());
		if ($this->getId() != '1') {
			$html.= $this->getRemoveLinkHtml();
		}
		return $html;
	}
	
	public function validate(Varien_Object $object)
	{
        /*getData(debug_backtrace());*/
		$all = $this->getAggregator()==='all';
		$true = (bool)$this->getValue();
		$found = false;
		foreach ($object->getAllItems() as $item) {
			$params = unserialize($item->getOptionByCode('info_buyRequest')->getValue());
			if(isset($params['free_catalog_gift']) || isset($params['freegift'])){								
				continue;
			}
			else{
				$found = $all;
				foreach ($this->getConditions() as $cond) {
					$validated = $cond->validate($item);
					if (($all && !$validated) || (!$all && $validated)) {
						$found = $validated;
						break;
					}
				}
				if (($found && $true) || (!$true && $found)) {
					break;
				}
			}
		}
		if ($found && $true) {
			return true;
		}
		elseif (!$found && !$true) {
			return true;
		}
		return false;
	}
}
