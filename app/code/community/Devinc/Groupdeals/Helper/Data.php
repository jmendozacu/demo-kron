<?php

class Devinc_Groupdeals_Helper_Data extends Mage_Core_Helper_Abstract
{    
	//check if extension is enabled
	public static function isEnabled()
	{
		$storeId = Mage::app()->getStore()->getId();
		$isModuleEnabled = Mage::getStoreConfig('advanced/modules_disable_output/Devinc_Groupdeals', $storeId);
		$isEnabled = Mage::getStoreConfig('groupdeals/configuration/enabled', $storeId);
		return ($isModuleEnabled == 0 && $isEnabled == 1);
	}
	
	public function getGroupdealAttributeSetId() {
		if ($attributeSetId = Mage::getStoreConfig('groupdeals/attribute_set_id')) {
			return $attributeSetId;
		} else {
			return Mage::getModel('eav/entity_attribute_set')->getCollection()->addFieldToFilter('attribute_set_name', 'Group Deal')->getFirstItem()->getId();
		}
	}
	
	public static function allowGiveAsGift()
	{
		return Mage::getStoreConfig('groupdeals/configuration/gift_to_friend');
	}
	
	public function getGroupdealPopupUrl()
	{
		return 'javascript:void(0);';
	}
	
	//save city in session for frontend display
	public function setCity($city = null) {
		if (isset($city)) {
			Mage::getSingleton('core/session')->setCity($city);
		}
	}
	
	public function getCity() {
		return Mage::getSingleton('core/session')->getCity();
	}
	
	//save region in session for frontend display
	public function setRegion($region = null) {
		if (isset($region)) {
			Mage::getSingleton('core/session')->setRegion($region);
		}
	}
	
	public function getRegion() {
		return Mage::getSingleton('core/session')->getRegion();
	}
	
	public function getCurrentDateTime($_storeId = null, $_format = 'Y-m-d H:i:s') {
		if (is_null($_storeId)) {
			$_storeId = Mage::app()->getStore()->getId();
		}
		$storeDatetime = new DateTime();
		$storeDatetime->setTimezone(new DateTimeZone(Mage::getStoreConfig('general/locale/timezone', $_storeId)));	
		
		return $storeDatetime->format($_format);
	}	
	
	//returns the utc date
	public function convertDateToUtc($datetime, $store_id = 0) {	
		$offset = $this->getTimezoneOffset(Mage::getStoreConfig('general/locale/timezone', $store_id),'UTC');
		$time=strtotime($datetime)+$offset;
		
  		return date('Y-m-d H:i:s',$time);
	}
	
	//returns the timezone offset
	public function getTimezoneOffset($remote_tz, $origin_tz = null) {
		if($origin_tz === null) {
			if(!is_string($origin_tz = date_default_timezone_get())) {
				return false; // A UTC timestamp was returned -- bail out!
			}
		}
		$origin_dtz = new DateTimeZone($origin_tz);
		$remote_dtz = new DateTimeZone($remote_tz);
		$origin_dt = new DateTime("now", $origin_dtz);
		$remote_dt = new DateTime("now", $remote_dtz);
		$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
		return $offset;
	}
	
	//returns the formated deal price
	public function getFormatedPrice($_product, $price, $storeId = null, $format = true, $includeContainer = false, $includingTax = true)
    {
    	$_taxHelper  = Mage::helper('tax');
	    $_priceInclTax = $_taxHelper->getPrice($_product, $price, $includingTax); 
	    
	    if ($this->getMagentoVersion()>1420 && Mage::helper('groupdeals')->getMagentoVersion()<1800) {
			return Mage::helper('core')->currencyByStore($_priceInclTax, $storeId, $format, $includeContainer);
		} else {
			return $this->currencyByStore($_priceInclTax, $storeId, $format, $includeContainer);			
		}
    }

    /**
     * Convert and format price value for specified store
     *
     * @param   float $value
     * @param   int|Mage_Core_Model_Store $store
     * @param   bool $format
     * @param   bool $includeContainer
     * @return  mixed
     */
    public static function currencyByStore($value, $store = null, $format = true, $includeContainer = true)
    {
        try {
            if (!($store instanceof Mage_Core_Model_Store)) {
                $store = Mage::app()->getStore($store);
            }

            $value = $store->convertPrice($value, $format, $includeContainer);
        }
        catch (Exception $e){
            $value = $e->getMessage();
        }

        return $value;
    }
	
	//$toDate format(year-month-day hour:minute:second) = 0000-00-00 00:00:00
    public function getCountdown($toDate, $countdownId = 'main', $finished = false, $params = false)
    {
    	//from/to date variables
		$fromDate = $this->getCurrentDateTime();
		$jsFromDate = date('F d, Y H:i:s', strtotime($fromDate));
		$jsToDate = date('F d, Y H:i:s', strtotime($toDate));
		if ($finished) {
			$toDate = $fromDate;
			$jsToDate = $jsFromDate;	
		}	
		
		$countdownType = Mage::getStoreConfig('groupdeals/configuration/countdown_type');
		//js configuration
		$jsTextColor = Mage::getStoreConfig('groupdeals/js_countdown_configuration/textcolor');
		$jsDaysText = Mage::getStoreConfig('groupdeals/js_countdown_configuration/days_text');
		
		//flash configuration		
		$displayDays = Mage::getStoreConfig('groupdeals/countdown_configuration/display_days');
		$bgMain = str_replace('#','0x',Mage::getStoreConfig('groupdeals/countdown_configuration/bg_main'));
		$bgColor = str_replace('#','0x',Mage::getStoreConfig('groupdeals/countdown_configuration/bg_color'));
		$digitColor = str_replace('#','0x',Mage::getStoreConfig('groupdeals/countdown_configuration/textcolor'));
		$alpha = Mage::getStoreConfig('groupdeals/countdown_configuration/alpha');
		$secText = Mage::getStoreConfig('groupdeals/countdown_configuration/sec_text');
		$minText = Mage::getStoreConfig('groupdeals/countdown_configuration/min_text');
		$hourText = Mage::getStoreConfig('groupdeals/countdown_configuration/hour_text');
		$daysText = Mage::getStoreConfig('groupdeals/countdown_configuration/days_text');
		$textColor = str_replace('#','0x',Mage::getStoreConfig('groupdeals/countdown_configuration/txt_color'));
				
		//flash params	
		if (isset($params['bg_main'])) $bgMain = str_replace('#','0x',$params['bg_main']);
		if (isset($params['text_color'])) $textColor = str_replace('#','0x',$params['text_color']);
    	$width = (isset($params['width'])) ? $params['width'] : '100%';
    	$height = (isset($params['height'])) ? $params['height'] : '100%';
    	
		//js params	
		if (isset($params['js_text_color'])) $jsTextColor = $params['js_text_color'];
		
		//get flash source
		$date1 = strtotime($fromDate);
	    $date2 = strtotime($toDate);	   
		$dateDiff = $date2 - $date1;
		
		if ($displayDays==1) {
			$fullDays = floor($dateDiff/(60*60*24));
			if ($fullDays<=0) {
				$swfPath = 'groupdeals/flash/countdown.swf';
			} else {
				$swfPath = 'groupdeals/flash/countdown_days.swf';
			} 
		} else {
			if ($dateDiff>0) {
				$diff = abs($dateDiff); 
				$years   = floor($diff / (365*60*60*24)); 
				$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
				$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
				$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
				
				$hoursLeft = $days*24+$hours;
				if ($hoursLeft<100) {
					$swfPath = 'groupdeals/flash/countdown_multiple_2.swf';	
				} else {
					$swfPath = 'groupdeals/flash/countdown_multiple_3.swf';		
				}
			} else {
				$swfPath = 'groupdeals/flash/countdown_multiple_2.swf';		
			}
		}	
		$store = Mage::app()->getStore();
		$source = Mage::getDesign()->getSkinUrl($swfPath, array('_area' => 'frontend', '_store' => $store));
		
		//encode flash variables
		//if you want to pass your custom variables to the countdown just add them to the array, at the end. Then you can call them in the countdown actionscript.
		$flashVars = array($fromDate, $toDate, $alpha, $bgColor, $digitColor, $bgMain, $textColor);	
		$variables = Mage::getModel('license/module')->encodeFlashVariables($flashVars);
		$textVariables = $secText.'|||'.$minText.'|||'.$hourText.'|||'.$daysText; 	
		
		$html = '<div class="countdown-container">
					<div id="countdown-'.$countdownId.'">
						<script type="text/javascript">
				 			var jsCountdown = new JsCountdown("'.$jsFromDate.'", "'.$jsToDate.'", "countdown-'.$countdownId.'", "'.$jsDaysText.'", "'.$jsTextColor.'");
				 		</script>
				 	</div>
				</div>';
		
		//if countdown type flash and flash present, replace default javascript countdown
		if (!isset($params['type'])) $params['type'] = '';
		if ($countdownType==1) {   
			$html .= '<script type="text/javascript">						
					     var params = {}; 	
					     var flashvars = {};
					     var attributes = {};
					     
 					     params.menu = "false"; 
 					     params.salign = "MT";
 					     params.allowFullscreen = "true";
					     if (navigator.userAgent.indexOf("Opera") <= -1) {
 					         params.wmode = "opaque";
					     }				 	
					     flashvars.vs = "'.$variables.'";
					     flashvars.smhd = "'.$textVariables.'";				 		
					     
					     swfobject.embedSWF("'.$source.'", "countdown-'.$countdownId.'", "'.$width.'", "'.$height.'", "9.0.0", false, flashvars, params, attributes);				 			
					 </script>';
		}
	
        return $html;
    }
    
    //called on product list pages
    public function getProductCountdown($_product, $_params = false, $_timeLeftText = true) {
		$html = '';	
		$statusArray = array();
		$statusArray[] = Devinc_Groupdeals_Model_Source_Status::STATUS_RUNNING;	
		$statusArray[] = Devinc_Groupdeals_Model_Source_Status::STATUS_ENDED;	
			
		if (in_array($_product->getGroupdealStatus(), $statusArray) && $_product->getGroupdealDatetimeTo() && Mage::helper('groupdeals')->isEnabled()) {
    		$finished = ($_product->isSaleable()) ? false : true;
    		$html .= ($_timeLeftText) ? '<b>'.$this->__('Time left to buy:').'</b>' : '';
			$html .= $this->getCountdown($_product->getGroupdealDatetimeTo(), $_product->getId(), $finished, $_params);
		}
				
		return $html;
    }
    
    //returns a city's main product url
    public function getCityUrl($_city, $_region = null, $_isMobile = false) {
    	$resource = Mage::getSingleton('core/resource');
    	if (is_null($_region)) {
			$crcCollection = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('city', $_city);		
		} else {			
			$crcCollection = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('region', $_region)->addFieldToFilter('city', $_city);	
		}
		$crcCollection->getSelect()->join(array('groupdeals'=>$resource->getTableName('groupdeals')), 'main_table.groupdeals_id = groupdeals.groupdeals_id', array('groupdeals.position'), null);		
		$crcCollection->setOrder('position', 'ASC')->setOrder('groupdeals_id', 'DESC');		

		$pastDeals = false;
		$queuedDeals = false;	
		$url = '';			
		$crcId = '';
		$groupdealId = '';
		$storeId = Mage::app()->getStore()->getId();

		foreach ($crcCollection as $crc) {	
			$crcId = $crc->getId();
			$product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($crc->getProductId());
			if ($product->getGroupdealStatus()==Devinc_Groupdeals_Model_Source_Status::STATUS_RUNNING) {
				$mainProduct = $product;
				$groupdealId = $crc->getGroupdealsId();
				$crcId = $crc->getId();
				break;
			} elseif ($product->getGroupdealStatus()==Devinc_Groupdeals_Model_Source_Status::STATUS_ENDED) {
				$pastDeals = true;
			} elseif ($product->getGroupdealStatus()==Devinc_Groupdeals_Model_Source_Status::STATUS_QUEUED) {
				$queuedDeals = true;
			}
		}
		
		if ($groupdealId!='') {					
			if (Mage::getStoreConfig('groupdeals/configuration/deals_view')==0 && !$_isMobile) {
				//$url = Mage::getModel('catalog/product_url')->getUrl($mainProduct, array('crc'=>$crcId));
				if ($mainProduct->getUrlPath()!='') {
					//$url = $mainProduct->getProductUrl();
					$url = substr(Mage::getUrl($mainProduct->getUrlPath()),0,-1).'?crc='.$crcId;
				} else {
					$url = Mage::getUrl('groupdeals/product/view', array('id'=>$mainProduct->getId(), 'groupdeals_id'=>$groupdealId, 'crc'=>$crcId));			
				}
			} else {
				$url = Mage::getUrl('groupdeals/product/list', array('crc'=>$crcId));
			}
		} elseif ($pastDeals) {
			$url = Mage::getUrl('groupdeals/product/recent', array('crc'=>$crcId));
		} elseif ($queuedDeals) {
			$url = Mage::getUrl('groupdeals/product/upcoming', array('crc'=>$crcId));
		}
		
		return $url;
	}
	
	public function getProductPlaceHolder($storeId) {
		$store = Mage::app()->getStore($storeId);
		$baseMediaUrl = Mage::getSingleton('catalog/product_media_config')->getBaseMediaUrl();
		if (Mage::getStoreConfig('catalog/placeholder/image_placeholder', $storeId)) {
            $productImage = $baseMediaUrl.'/placeholder/'.Mage::getStoreConfig('catalog/placeholder/image_placeholder', $storeId);
        } else {
            $productImage = Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/image.jpg', array('_area' => 'frontend', '_store' => $store));
        }
        
        return $productImage;
	}
	
	//get url by store settings
	public function getStoreUrl($_path, $_storeId = null, $_storeToUrl = false, $params = array()) {
		if (is_null($_storeId)) {
			$store = Mage::app()->getStore();
		} else {
			$store = Mage::app()->getStore($_storeId);		
		}
		$params['_store_to_url'] = $_storeToUrl;
		$params['_store'] = $store;
		$params['_secure'] = $store->isFrontUrlSecure();
		
		$url = Mage::getModel('core/url')->getUrl($_path, $params);
		
				
		return $this->applyUrlRewrite($url, $store->getId());
	}
	
	//index.php fix for email templates; applies seo url rewrite
	public function applyUrlRewrite($_url, $_storeId) {
		$urlRewrites = Mage::getStoreConfig('web/seo/use_rewrites', $_storeId);
		if ($urlRewrites) {
			$_url = str_replace('index.php/','',$_url);
		} elseif (!$urlRewrites && strpos($_url, 'index.php/')===false) {			
			$baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
			$_url = str_replace($baseUrl,$baseUrl.'index.php/',$_url);		
		}
		
		return $_url;
	}
	
	//translate functions; used to translate the coupons in Model/Coupons.php 
    public function translate($params, $_storeId = null)
    {        
    	return Mage::getModel('license/module')->translate($params, $_storeId);
    }
    
    public function getMagentoVersion() {
		return (int)str_replace(".", "", Mage::getVersion());
    }
	
	//detect if ie is 9 or higher
	public function displayInIe()
	{
	    if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') !== false)) {
	        return false;
	    } else {
	        return true;
	    }
	}
	
	//detect if ie
	public function isIE() {
	    if(preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])) {
	        return true;
	    }
	    	
	    return false;
	}
	
	/**
 	* @author Ivan Weiler <ivan.weiler@gmail.com> facebook connect functions
 	*/
	public function getConnectUrl()
	{
		return $this->_getUrl('groupdeals/customer_account/connect', array('_secure'=>true));
	}
	
	public function isFacebookCustomer($customer)
	{
		if($customer->getFacebookUid()) {
			return true;
		}
		return false;
	}
}