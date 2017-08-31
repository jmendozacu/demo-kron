<?php

class MW_FreeGift_Model_Rule extends Mage_Rule_Model_Rule
{
    protected $_productIds;
    
	public function _construct()
    {
        parent::_construct();
        $this->_init('freegift/rule');
        $this->_eventPrefix = 'freegift_rule';
    }
	
	public function getNow()
    {
        if (!$this->_now) {
            return now();
        }
        return $this->_now;
    }

    public function setNow($now)
    {
        $this->_now = $now;
    }
    
    public function getConditionsInstance()
    {
        return Mage::getModel('catalogrule/rule_condition_combine');
    }
	public function getMatchingProductIds()
    {
        if (is_null($this->_productIds)) {
            $this->_productIds = array();
            $this->setCollectedAttributes(array());
            $websiteIds = is_array($this->getWebsiteIds())?$this->getWebsiteIds():explode(',', $this->getWebsiteIds());
            if ($websiteIds) {
                $productCollection = Mage::getResourceModel('catalog/product_collection');
                $productCollection->addWebsiteFilter($websiteIds);
                
                $this->getConditions()->collectValidatedAttributes($productCollection);

                Mage::getSingleton('core/resource_iterator')->walk(
                    $productCollection->getSelect(),
                    array(array($this, 'callbackValidateProduct')),
                    array(
                        'attributes' => $this->getCollectedAttributes(),
                        'product'    => Mage::getModel('catalog/product'),
                    )
                );
            }
        }
        return $this->_productIds;
    }
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);
        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }
}