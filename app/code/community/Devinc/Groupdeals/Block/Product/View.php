<?php
class Devinc_Groupdeals_Block_Product_View extends Mage_Catalog_Block_Product_View
{   
	//add groupdeal breadcrumbs
    protected function _prepareLayout()
    {
		$crcId = $this->getRequest()->getParam('crc', false);
        if ($crcId && $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
        	$breadcrumbsBlock->addCrumb('home', array(
                'label'=>Mage::helper('catalog')->__('Home'),
                'title'=>Mage::helper('catalog')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ));
            
            $breadcrumbsBlock->addCrumb('groupdeals', array(
                'label'=>Mage::helper('groupdeals')->__('Group Deals'),
                'title'=>Mage::helper('groupdeals')->__('Group Deals'),
				'link'=>"javascript:popup.showPopup();"
            ));
            
            $city = Mage::helper('groupdeals')->getCity();
            $breadcrumbsBlock->addCrumb('city', array(
                'label'=>Mage::helper('groupdeals')->__($city),
                'title'=>Mage::helper('groupdeals')->__($city),
			    'link'=>Mage::getUrl('groupdeals/product/list', array('crc'=>rawurlencode($crcId)))
            ));    
                    
            //unregister current category to not affect the urls for the deals that aren't under categories
            Mage::unregister('current_category');        
        } else {
        	$this->getLayout()->createBlock('catalog/breadcrumbs');            
        }
        return parent::_prepareLayout();
    }
    
    public function getJsonConfig() 
    {
    	$jsOnConfig = parent::getJsonConfig();
    	//$jsOnConfig = str_replace('"requiredPrecision":2', '"requiredPrecision":0', $jsOnConfig);
	    return $jsOnConfig;
    }

    public function getGroupdeal()
    {
        return Mage::registry('groupdeals');
    }
	
    public function getDiscountPercent($_product)
    {		
		$discount = ($_product->getPrice()-$_product->getFinalPrice())*100/$_product->getPrice();
		
		return number_format($discount,0).'%';
    }	    
	
    public function getSoldQty($_groupdeal)
    {
		$soldQty = Mage::getModel('groupdeals/groupdeals')->getGroupdealsSoldQty($_groupdeal);	
		
        return $soldQty;
    }	

    //check if product can be emailed to friend
    public function canEmailToFriend()
    {
        $sendToFriend = Mage::helper('sendfriend');
        return $sendToFriend && $sendToFriend->isEnabled();
    }
	
    public function getTippingTime($_groupdeal)
    {
		$tippingTime = Mage::getModel('groupdeals/groupdeals')->getTippingTime($_groupdeal);	
		
        return $tippingTime;
    }
    
    public function hasVisibleFrontendAttributes() {
    	$attributesBlock = new Mage_Catalog_Block_Product_View_Attributes;
    	
    	return $attributesBlock->getAdditionalData();
    }
    
    public function hasUpsellProducts($_product) {
    	if (count($_product->getUpSellProductCollection()->addStoreFilter()->load()->getItems())>0) {
    		return true;
    	}
    	
    	return false;
    }
    
    public function allowFacebookComments() {
    	if (Mage::helper('groupdeals')->displayInIe()) {
    		return Mage::getStoreConfig('groupdeals/configuration/enable_facebook_comments');
    	} else {
    		return false;
    	}
    }
    
    public function getMerchant($_groupdeal)
    {
        return Mage::getModel('groupdeals/merchants')->load($_groupdeal->getMerchantId());
    } 
    
    public function getMerchantName()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeal = $this->getGroupdeal();
		$_merchant = $this->getMerchant($_groupdeal);	
		
	    $merchant_name = Mage::getModel('license/module')->getDecodeString($_merchant->getName(), $storeId);
	
        return $merchant_name;
    } 
    
    public function getMerchantLogo()
    {
		$_groupdeal = $this->getGroupdeal();
		$_merchant = $this->getMerchant($_groupdeal);	
		
		$path = '';
		if ($_merchant->getMerchantLogo()!='') {
			$path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$_merchant->getMerchantLogo();
		}
	
        return $path;
    } 
    
    public function getMerchantDescription()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeal = $this->getGroupdeal();
		$_merchant = $this->getMerchant($_groupdeal);	
		
	    $merchant_description = Mage::getModel('license/module')->getDecodeString($_merchant->getDescription(), $storeId);
	
        return $merchant_description;
    } 
    
    public function getBusinessHours()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeal = $this->getGroupdeal();
		$_merchant = $this->getMerchant($_groupdeal);	
		
	    $merchant_hours = Mage::getModel('license/module')->getDecodeString($_merchant->getBusinessHours(), $storeId);
	
        return $merchant_hours;
    } 
    
    public function getWebsite()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeal = $this->getGroupdeal();
		$_merchant = $this->getMerchant($_groupdeal);	
		
	    $merchant_website = Mage::getModel('license/module')->getDecodeString($_merchant->getWebsite(), $storeId);
		if ($merchant_website!='' && strpos($merchant_website, 'http')==false) {
			$merchant_website = 'http://'.$merchant_website;
		}
		
        return $merchant_website;
    } 
    
    public function getFacebook()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeal = $this->getGroupdeal();
		$_merchant = $this->getMerchant($_groupdeal);	
		
	    $merchant_facebook = Mage::getModel('license/module')->getDecodeString($_merchant->getFacebook(), $storeId);
	
        return $merchant_facebook;
    } 
    
    public function getTwitter()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeal = $this->getGroupdeal();
		$_merchant = $this->getMerchant($_groupdeal);	
		
	    $merchant_twitter = Mage::getModel('license/module')->getDecodeString($_merchant->getTwitter(), $storeId);
	
        return $merchant_twitter;
    } 
    
    public function getEmail()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeal = $this->getGroupdeal();
		$_merchant = $this->getMerchant($_groupdeal);	
		
	    $merchant_email = Mage::getModel('license/module')->getDecodeString($_merchant->getEmail(), $storeId);
	
        return $merchant_email;
    } 
    
    public function getPhone()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeal = $this->getGroupdeal();
		$_merchant = $this->getMerchant($_groupdeal);	
		
	    $merchant_phone = Mage::getModel('license/module')->getDecodeString($_merchant->getPhone(), $storeId);
	
        return $merchant_phone;
    } 
    
    public function getMobile()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeal = $this->getGroupdeal();
		$_merchant = $this->getMerchant($_groupdeal);	
		
	    $merchant_mobile = Mage::getModel('license/module')->getDecodeString($_merchant->getMobile(), $storeId);
	
        return $merchant_mobile;
    } 
    
    public function getAddressCollection($_merchant)
    {
		if ($_merchant->getAddress()!='') {
			$addresses = explode('_;_',$_merchant->getAddress());	
			$newAddresses = array();
			foreach ($addresses as $address) {
				$newAddresses[] = str_replace("'", "", $address);
			}		
			return $newAddresses;
		} else {
			return array();
		}
    } 
    
    public function getRedeem()
    {
		$storeId = Mage::app()->getStore()->getId();
		$_groupdeal = $this->getGroupdeal();
		$_merchant = $this->getMerchant($_groupdeal);	
		
	    $merchant_redeem = Mage::getModel('license/module')->getDecodeString($_merchant->getRedeem(), $storeId);
	
        return $merchant_redeem;
    } 

}