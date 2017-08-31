<?php

class MW_FreeGift_Model_Salesrule extends Mage_Rule_Model_Rule
{
	const COUPON_TYPE_SPECIFIC  = 2;
	
	public function _construct()
    {
        parent::_construct();
        $this->_init('freegift/salesrule');
    }
	
    public function getConditionsInstance()
    {
        return Mage::getModel('salesrule/rule_condition_combine');
    }
	
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        if (isset($arr['actions'])) {
            $this->getActions()->setActions(array())->loadArray($arr['actions'][1], 'actions');
        }
        if (isset($rule['store_labels'])) {
            $this->setStoreLabels($rule['store_labels']);
        }
        return $this;
    }
    
    protected function _afterSave()
    {
        //Saving attributes used in rule
        $ruleProductAttributes = $this->_getUsedAttributes($this->getConditionsSerialized());
        if (count($ruleProductAttributes)) {
            $this->getResource()->setActualProductAttributes($this, $ruleProductAttributes);
        }
        return parent::_afterSave();		
        return $this;
    }
    
    protected function _getUsedAttributes($serializedString)
    {
        $result = array();
        if (preg_match_all('~s:32:"salesrule/rule_condition_combine";s:9:"attribute";s:\d+:"(.*?)"~s',
            $serializedString, $matches)){
            foreach ($matches[1] as $offset => $attributeCode) {
                $result[] = $attributeCode;
            }
        }
        return $result;
    }
	
	public static function getCouponMassGenerator()
    {	
        return Mage::getSingleton('freegift/coupon_massgenerator');
    }
	
    public static function getCouponCodeGenerator()
    {
        if (!self::$_couponCodeGenerator) {
            return Mage::getSingleton('freegift/coupon_codegenerator', array('length' => 16));
        }
        return self::$_couponCodeGenerator;
    }
	
	public static function setCouponCodeGenerator(MW_FreeGift_Model_Coupon_CodegeneratorInterface $codeGenerator)
    {
        self::$_couponCodeGenerator = $codeGenerator;
    }
	
    public function getPrimaryCoupon()
    {
        if ($this->_primaryCoupon === null) {
            $this->_primaryCoupon = Mage::getModel('freegift/coupon');
            $this->_primaryCoupon->loadPrimaryByRule($this->getId());
            $this->_primaryCoupon->setRule($this)->setIsPrimary(true);
        }
        return $this->_primaryCoupon;
    }
	
    public function getCoupons()
    {	
        if ($this->_coupons === null) {
            $collection = Mage::getResourceModel('freegift/coupon_collection');
            /** @var Mage_SalesRule_Model_Resource_Coupon_Collection */
            $collection->addRuleToFilter($this);
            $this->_coupons = $collection->getItems();
        }
        return $this->_coupons;
    }
	
    public function getCouponTypes()
    {
        if ($this->_couponTypes === null) {
            $this->_couponTypes = array(
                MW_FreeGift_Model_Rule::COUPON_TYPE_NO_COUPON => Mage::helper('freegift')->__('No Coupon'),
                MW_FreeGift_Model_Rule::COUPON_TYPE_SPECIFIC  => Mage::helper('freegift')->__('Specific Coupon'),
            );
            $transport = new Varien_Object(array(
                'coupon_types'                => $this->_couponTypes,
                'is_coupon_type_auto_visible' => false
            ));
            Mage::dispatchEvent('salesrule_rule_get_coupon_types', array('transport' => $transport));
            $this->_couponTypes = $transport->getCouponTypes();
            if ($transport->getIsCouponTypeAutoVisible()) {
                $this->_couponTypes[MW_FreeGift_Model_Rule::COUPON_TYPE_AUTO] = Mage::helper('freegift')->__('Auto');
            }
        }
        return $this->_couponTypes;
    }

    public function acquireCoupon($saveNewlyCreated = true, $saveAttemptCount = 10)
    {
        if ($this->getCouponType() == self::COUPON_TYPE_NO_COUPON) {
            return null;
        }
        if ($this->getCouponType() == self::COUPON_TYPE_SPECIFIC) {
            return $this->getPrimaryCoupon();
        }
        /** @var Mage_SalesRule_Model_Coupon $coupon */
        $coupon = Mage::getModel('freegift/coupon');
        $coupon->setRule($this)            
            ->setUsageLimit($this->getUsesPerCoupon() ? $this->getUsesPerCoupon() : null)
            ->setUsagePerCustomer($this->getUsesPerCustomer() ? $this->getUsesPerCustomer() : null)
            ->setExpirationDate($this->getToDate());

        $couponCode = self::getCouponCodeGenerator()->generateCode();
        $coupon->setCode($couponCode);

        $ok = false;
        if (!$saveNewlyCreated) {
            $ok = true;
        } else if ($this->getId()) {
            for ($attemptNum = 0; $attemptNum < $saveAttemptCount; $attemptNum++) {
                try {
                    $coupon->save();
                } catch (Exception $e) {
                    if ($e instanceof Mage_Core_Exception || $coupon->getId()) {
                        throw $e;
                    }
                    $coupon->setCode(
                        $couponCode .
                        self::getCouponCodeGenerator()->getDelimiter() .
                        sprintf('%04u', rand(0, 9999))
                    );
                    continue;
                }
                $ok = true;
                break;
            }
        }
        if (!$ok) {
            Mage::throwException(Mage::helper('freegift')->__('Can\'t acquire coupon.'));
        }

        return $coupon;
    }
}