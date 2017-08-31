<?php
class MW_FreeGift_Block_Quote extends Mage_Core_Block_Template
{
    public function _construct()
    {
        $this->setTemplate('mw_freegift/quote.phtml');
    }
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    public function getAllActiveRules()
    {
        $quote           = Mage::getSingleton('checkout/session')->getQuote();
        $websiteId       = Mage::app()->getStore($quote->getStoreId())->getWebsiteId();
        $customerGroupId = $quote->getCustomerGroupId() ? $quote->getCustomerGroupId() : 0;       
        $flagRule            = Mage::getSingleton('core/session')->getFlagRule();
        
        $arrRule = explode(",",$flagRule);
		$allowRule = $arrRule;
        $collection = Mage::getModel('freegift/salesrule')->getCollection()->setValidationFilter($websiteId, $customerGroupId);

        $aplliedRuleIds = Mage::getSingleton('checkout/session')->getQuote()->getFreegiftAppliedRuleIds();
		$arrRuleApllieds = explode(',',$aplliedRuleIds);	
		
        $collection->getSelect()->where('((discount_qty > times_used) or (discount_qty=0))');       
        $collectionSaleRule = Mage::getModel('freegift/salesrule')->getCollection()->setOrder("sort_order", "ASC");
        $collectionSaleRule->getSelect()->where('is_active = 1');
        $listSaleRule = array();
        foreach ($collectionSaleRule as $saleRule) {
			if(in_array($saleRule->getId(),$arrRuleApllieds)){
				if($saleRule->getStopRulesProcessing()){
					$listSaleRule[] = $saleRule->getId();
					break;
				}
			}						           
			$listSaleRule[] = $saleRule->getId();
        }
		$collection->addFieldToFilter('rule_id', array(
                    'in' => $listSaleRule
        ));
		
        if (sizeof($arrRuleApllieds)){				
            $collection->addFieldToFilter('rule_id', array(
                    'nin' => $arrRuleApllieds
         	));			
        }        
        return $collection;
    }
    public function getRandomRule()
    {
        $ids      = $this->getAllActiveRules()->getAllIds();
        $rand_key = array_rand($ids);
        return Mage::getModel('freegift/salesrule')->load($ids[$rand_key]);
    }
    
    public function _toHtml()
    {
        if (!Mage::getStoreConfig('freegift/config/enabled'))
            return '<div class="freegift_rules_container"></div>';
        if (!sizeof($this->getAllActiveRules()))
            return '<div class="freegift_rules_container"></div>';
        $html = $this->renderView();
        
        return $html;
    }
} 