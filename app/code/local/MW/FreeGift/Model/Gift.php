<?php
class MW_FreeGift_Model_Gift extends Varien_Object
{
    protected $_rules;

    protected $_freegift_ids;

    protected $_max_free_item;

    protected $_apllied_rules;

    protected $_apllied_rule_ids;

    protected $_freeproduct;

	protected function _getProductCollection(){
    	$visibility = array(
        	Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
        	Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );
		$storeId = Mage::app()->getStore()->getId();
		$products = Mage::getResourceModel('reports/product_collection')
						->addAttributeToSelect('*')
					    ->setStoreId($storeId)
                        ->addStoreFilter($storeId)
					    ->addAttributeToFilter('visibility', $visibility);
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
		return $products;
    }

    protected function _getCheckoutSession()
    {
    	return Mage::getSingleton('checkout/session');
    }

    public function init()
    {
    	$quote = Mage::getSingleton('checkout/session')->getQuote();
        $websiteId = Mage::app()->getStore($quote->getStoreId())->getWebsiteId();
        $customerGroupId = $quote->getCustomerGroupId()?$quote->getCustomerGroupId():0;
    	$this->setWebsiteId($websiteId)
            ->setCustomerGroupId($customerGroupId);
        $key = 'freegift_'.$websiteId . '_' . $customerGroupId;
        if (!isset($this->_rules[$key])) {
        	$collection =Mage::getResourceModel('freegift/rule_collection')
                ->setValidationFilter($websiteId, $customerGroupId);
            $collection->getSelect()->where('((discount_qty > times_used) or (discount_qty=0))');
            $collection->load();
            $this->_rules[$key] = $collection;

        }
        return $this;
    }

	/**
     * Get rules collection for current object state
     *
     * @return Mage_SalesRule_Model_Mysql4_Rule_Collection
     */
    protected function _getRules()
    {
        $key = 'freegift_'.$this->getWebsiteId() . '_' . $this->getCustomerGroupId();
        return $this->_rules[$key];
    }

    protected function _canProcessRule(Xmage_FreeGift_Model_Rule $rule, Mage_Catalog_Model_Product $product)
    {
    	//var_dump($rule->getMatchingProductIds());exit;
    	return in_array($product->getId(),$rule->load($rule->getId())->getMatchingProductIds());
    }


    public function getFreeGifts(Mage_Catalog_Model_Product $product)
    {
    	foreach ($this->_getRules() as $rule) {
    		if(!in_array($product->getId(),$rule->load($rule->getId())->getMatchingProductIds())) {
    			continue;
    		}
    		$this->_apllied_rules[] = $rule;
    		$this->_apllied_rule_ids[] = $rule->getId();
			$this->_freegift_ids = $this->mergeIds($this->_freegift_ids, $rule->getData('gift_product_ids'),false);
            if ($rule->getStopRulesProcessing()) {
                break;
            }
    	}
    	$this->_max_free_item = 1;
    	return $this->_freegift_ids;
    }

    public function getAplliedRules()
    {
    	if($this->_apllied_rules) return $this->_apllied_rules;
    	return false;
    }

	public function getAplliedRuleIds()
    {
    	if($this->_apllied_rule_ids) return $this->_apllied_rule_ids;
    	return false;
    }

    public function getNumberOfAddedFreeItems()
    {
    	if(!$this->_num_of_added_items){
	    	$this->_num_of_added_items = 0;
	    	$items = $this->_getCheckoutSession()->getQuote()->getAllVisibleItems();
	    	foreach($items as $item)
	    	{
	    		if($item->getOptionByCode('free_catalog_gift')) $this->_num_of_added_items++;
	    	}
    	}
    	return $this->_num_of_added_items;
    }

    public function mergeIds($a1, $a2, $asString = true)
    {
        if (!is_array($a1)) {
            $a1 = empty($a1) ? array() : explode(',', $a1);
        }
        if (!is_array($a2)) {
            $a2 = empty($a2) ? array() : explode(',', $a2);
        }
        $a = array_unique(array_merge($a1, $a2));
        if ($asString) {
           $a = implode(',', $a);
        }
        return $a;
    }
}