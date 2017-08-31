<?php

class Devinc_Groupdeals_Model_Notifications extends Mage_Core_Model_Abstract
{	
	const ITERATIONS = 10;
	const STATUS_RUNNING = Devinc_Groupdeals_Model_Source_Status::STATUS_RUNNING;
	const STATUS_ENDED = Devinc_Groupdeals_Model_Source_Status::STATUS_ENDED;
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('groupdeals/notifications');
    }
    
    //create deal notifications
    public function createNotifications()
    {		
    	$allowNewDealNotification = Mage::getStoreConfig('groupdeals/notifications/email_new_deal');
    	$allowTargetMetNotification = Mage::getStoreConfig('groupdeals/notifications/email_target_met');
    	$allowDealOverNotification = Mage::getStoreConfig('groupdeals/notifications/email_deal_over');
    	$isEnabled = Mage::helper('groupdeals')->isEnabled();
		if (($allowNewDealNotification || $allowTargetMetNotification || $allowDealOverNotification) && $isEnabled) {
			$groupdealCollection = Mage::getModel('groupdeals/groupdeals')->getCollection();
	
			if (count($groupdealCollection)>0) {
				foreach ($groupdealCollection as $groupdeal) {
					$websiteIds = Mage::getModel('core/website')->getCollection()->addFieldToFilter('website_id', array('neq' => 0))->getColumnValues('website_id');
					$soldQty = Mage::getModel('groupdeals/groupdeals')->getGroupdealsSoldQty($groupdeal);
					foreach ($websiteIds as $websiteId) 
					{	
						$store = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $websiteId)->addFieldToFilter('is_active', 1)->getFirstItem();
						if ($store->getId()) {
							$product = Mage::getModel('catalog/product')->setStoreId($store->getId())->load($groupdeal->getProductId());
							$productWebsiteIds = $product->getWebsiteIds();
							
							//get previous notifications if they exist
							$newDeal = Mage::getModel('groupdeals/notifications')->getCollection()->addFieldToFilter('type', 'new_deal')->addFieldToFilter('groupdeals_id', $groupdeal->getId())->addFieldToFilter('website_id', $websiteId)->getFirstItem();
							
							$targetMet = Mage::getModel('groupdeals/notifications')->getCollection()->addFieldToFilter('type', 'limit_met')->addFieldToFilter('groupdeals_id', $groupdeal->getId())->addFieldToFilter('website_id', $websiteId)->getFirstItem();
							
							$dealOver = Mage::getModel('groupdeals/notifications')->getCollection()->addFieldToFilter('type', 'deal_over')->addFieldToFilter('groupdeals_id', $groupdeal->getId())->addFieldToFilter('website_id', $websiteId)->getFirstItem();
						
							//get subscriber collection for this website
							$storesIds = Mage::getModel('core/store')->getCollection()->addFieldToFilter('website_id', $websiteId)->addFieldToFilter('is_active', 1)->getColumnValues('store_id');		
							
							$subscriberIds = array();
							$cities = Mage::getModel('groupdeals/crc')->getCitiesArray($groupdeal->getId());
							foreach($cities as $city) {							
								$tempSubscriberIds = Mage::getModel('groupdeals/subscribers')->getCollection()->addFieldToFilter('city', $city)->addFieldToFilter('store_id', array('in', $storesIds))->getColumnValues('subscriber_id');
								$subscriberIds = array_merge($subscriberIds, $tempSubscriberIds);
							}
								
							//create new deal notification
							if ($allowNewDealNotification && $newDeal->getId()=='' && $product->getGroupdealStatus()==self::STATUS_RUNNING && in_array($websiteId, $productWebsiteIds) && count($subscriberIds)>0) {	
								$subscriberIdsString = implode(',', $subscriberIds);							
								Mage::getModel('groupdeals/notifications')
								    ->setGroupdealsId($groupdeal->getId())
								    ->setWebsiteId($websiteId)
								    ->setType('new_deal')
								    ->setUnnotifiedSubscriberIds($subscriberIdsString)
								    ->setStatus('pending')
								    ->save();														
							}
							
							//create target met notification
							if ($allowTargetMetNotification && $targetMet->getId()=='' && $groupdeal->getMinimumQty()<=$soldQty && in_array($websiteId, $productWebsiteIds) && count($subscriberIds)>0) {	
								$subscriberIdsString = implode(',', $subscriberIds);								
								Mage::getModel('groupdeals/notifications')
								    ->setGroupdealsId($groupdeal->getId())
								    ->setWebsiteId($websiteId)
								    ->setType('limit_met')
								    ->setUnnotifiedSubscriberIds($subscriberIdsString)
								    ->setStatus('pending')
								    ->save();														
							}
							
							//create deal over notification
							if ($allowDealOverNotification && $dealOver->getId()=='' && $product->getGroupdealStatus()==self::STATUS_ENDED && in_array($websiteId, $productWebsiteIds) && count($subscriberIds)>0) {	
								$subscriberIdsString = implode(',', $subscriberIds);								
								Mage::getModel('groupdeals/notifications')
								    ->setGroupdealsId($groupdeal->getId())
								    ->setWebsiteId($websiteId)
								    ->setType('deal_over')
								    ->setUnnotifiedSubscriberIds($subscriberIdsString)
								    ->setStatus('pending')
								    ->save();														
							}
						}
					}
				}
			}
		}
	}
	
	//email the notifications to subscribers
    public function notify()
    {					
		$notification = Mage::getModel('groupdeals/notifications')->getCollection()->setOrder('notification_id', 'ASC')->addFieldToFilter('status', 'pending')->getFirstItem();
		
		if ($notification->getId()!='') {					
			$groupdeal = Mage::getModel('groupdeals/groupdeals')->load($notification->getGroupdealsId());
			
			$unnotifiedIds = explode(',', $notification->getUnnotifiedSubscriberIds());
			$remainingIds = $unnotifiedIds;
			$notifiedIdsString = $notification->getNotifiedSubscriberIds();
			$notifiedIds = array();
			if ($notifiedIdsString!='') {
				$notifiedIds = explode(',', $notifiedIdsString);
			}			
			$i = 0;
			$unnotifiedIdsCount = count($unnotifiedIds);
			
			foreach ($unnotifiedIds as $subscriberId) {						
				$i++;	
				$notifiedIds[] = $subscriberId;	
						
				$subscriber = Mage::getModel('groupdeals/subscribers')->load($subscriberId);	
				$crc = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('city', $subscriber->getCity())->addFieldToFilter('groupdeals_id', $groupdeal->getId())->getFirstItem();
				$storeId = $subscriber->getStoreId();	
				$store = Mage::app()->getStore($storeId);					
				$sender = Mage::getStoreConfig('groupdeals/notifications/email_sender', $storeId);
				$data['email'] = Mage::getStoreConfig('trans_email/ident_'.$sender.'/email', $storeId);		
				
				//get product/groupdeal info	
				$product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($groupdeal->getProductId());
								
				$data['name'] = $product->getName();
				$data['city'] = Mage::helper('groupdeals')->translate(array($crc->getCity()), $storeId);		
				
				//get product url with store code and city parameters
				$data['url'] = Mage::getModel('catalog/product_url')->getUrl($product, array('_store_to_url'=>true, '_nosid' => true)).'?crc='.$crc->getId(); 
				$data['url'] = Mage::helper('groupdeals')->applyUrlRewrite($data['url'], $storeId);	
				$data['logo'] = Mage::getDesign()->getSkinUrl(Mage::getStoreConfig('design/header/logo_src', $storeId), array('_area' => 'frontend', '_store' => $store));
				
				$data['unsubscribe_url'] = Mage::helper('groupdeals')->getStoreUrl('groupdeals/subscriber/unsubscribe', $storeId, true, array('subscriber_id' => base64_encode($subscriberId)));
				
				$data['website_url'] = Mage::helper('groupdeals')->getStoreUrl('', $storeId, true);  
				$data['website'] = str_replace('index.php/','',str_replace('http://','',Mage::helper('groupdeals')->getStoreUrl('', $storeId, false)));  
				
				//$data['email'] = Mage::getStoreConfig('contacts/email/recipient_email', $storeId);  					
				$data['date'] = Mage::helper('groupdeals')->getCurrentDateTime($storeId, 'd/m/Y');  
				
				// Get Prices with taxes
				$data['special_price'] = Mage::helper('groupdeals')->getFormatedPrice($product, $product->getSpecialPrice(), $storeId);  
				$data['price'] = Mage::helper('groupdeals')->getFormatedPrice($product, $product->getPrice(), $storeId);  
				$discount = ($product->getPrice()-$product->getSpecialPrice())*100/$product->getPrice();
				$data['discount'] = number_format($discount,0).'%';
				$data['you_save'] = Mage::helper('groupdeals')->getFormatedPrice($product, ($product->getPrice()-$product->getSpecialPrice()), $storeId);  
					
				//get merchant info
				if ($groupdeal->getMerchantId()!=0) {
					$merchant = Mage::getModel('groupdeals/merchants')->load($groupdeal->getMerchantId());	
					$data['has_merchant'] = true;
					$data['merchant_name'] = Mage::getModel('license/module')->getDecodeString($merchant->getName(),$storeId);  
					$data['merchant_phone'] = Mage::getModel('license/module')->getDecodeString($merchant->getPhone(),$storeId);  
					$data['merchant_mobile'] = Mage::getModel('license/module')->getDecodeString($merchant->getMobile(),$storeId);  
					$data['merchant_email'] = Mage::getModel('license/module')->getDecodeString($merchant->getEmail(),$storeId);  
					$data['merchant_website'] = Mage::getModel('license/module')->getDecodeString($merchant->getWebsite(),$storeId);  
					if($data['merchant_website']!=''){
						$data['has_merchant_website'] = true;
					} else {
						$data['has_merchant_website'] = false;
					}
					
					$address_string = $merchant->getAddress();
					if (isset($address_string) && $address_string!='') {
						$address = explode('_;_',$address_string);
						for ($j = 0; $j<count($address); $j++) {
							if ($address[$j]!='') {
								$data['merchant_address'.($j+1)] = ($j+1).'. '.$address[$j].'<br/>';
							}
						}
					} else {
						$data['merchant_address1'] = $merchant->getRedeem();
					}	
					
					$data['merchant_description'] = Mage::getModel('license/module')->getDecodeString($merchant->getDescription(),$storeId);
				} else {
					$data['has_merchant'] = false;				
				}
				
				//get side deals
				$productIds = Mage::getModel('groupdeals/crc')->getCollection()->addFieldToFilter('city', $crc->getCity())->addFieldToFilter('product_id', array('neq'=>$product->getId()))->setOrder('groupdeals_id', 'DESC')->getColumnValues('product_id');
				
				$data['side_deals'] = '';
				$sidedealsNumber = Mage::getStoreConfig('groupdeals/configuration/sidedeals_number');
				if (count($productIds)>0 && $sidedealsNumber>0) {
					$k = 0; 
					foreach ($productIds as $productId) { 
						$sideDeal = Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId);
						
						//get side deal image
						if ($sideDeal->getThumbnail()!='no_selection' && $sideDeal->getThumbnail()){
							$sideDealImage = Mage::helper('catalog/image')->init($sideDeal, 'thumbnail');
						} else {
							$sideDealImage = Mage::helper('groupdeals')->getProductPlaceHolder($storeId);
						}
						//get side deal url with store code and city parameters
						$sideDealUrl = Mage::getModel('catalog/product_url')->getUrl($sideDeal, array('_store_to_url'=>true, '_nosid' => true)); 	
						$sideDealUrl = Mage::helper('groupdeals')->applyUrlRewrite($sideDealUrl, $storeId).'?crc='.$crc->getId();
				
						if ($sideDeal->getGroupdealStatus()==self::STATUS_RUNNING) {   
							$k++;  						 
							$data['side_deals'] .='
								<tr style="border-bottom:1px solid #F0F1F3;">
									<td width="35%" align="center" style="font-weight:bold; padding:5px 5px 5px 10px;">
										<img width="100%" src="'.$sideDealImage.'">
									</td>
									<td width="65%" align="left" style="font-weight:bold;padding:5px 10px 5px 5px; font-size:11px;" >
										<a style="color:#0981BE;" href="'.$sideDealUrl.'">'.$sideDeal->getName().'</a>
									</td>
								</tr>';   
							if ($sidedealsNumber<=$k) break;    
						}
					}	
				}			
				if($data['side_deals']!=''){
					$data['merchant_description_width'] = '65%';
					$data['has_side_deals'] = true;
				} else {
					$data['merchant_description_width'] = '100%';
					$data['has_side_deals'] = false;
				}
				
				$data['all_deals_url'] = Mage::helper('groupdeals')->getStoreUrl('groupdeals/product/list', $storeId, true, array('crc' => $crc->getId()));
				$data['recent_deals_url'] = Mage::helper('groupdeals')->getStoreUrl('groupdeals/product/recent', $storeId, true, array('crc' => $crc->getId()));
				$data['contact_us_url'] = Mage::helper('groupdeals')->getStoreUrl('contacts', $storeId, true);
					
				//get product image
				if ($product->getImage()!='no_selection' && $product->getImage()){
					$data['image_url'] = Mage::helper('catalog/image')->init($product, 'image');
				} else {
					$data['image_url'] = Mage::helper('groupdeals')->getProductPlaceHolder($storeId);
				}	
				
				//email notification
				$postObject = new Varien_Object();
				$postObject->setData($data);	 
				
				$template = 'groupdeals_notifications_email_new_deal_template';
				if ($notification->getType()=='new_deal') {	
					$template = Mage::getStoreConfig('groupdeals/notifications/email_new_deal_template', $storeId);
				} elseif ($notification->getType()=='limit_met') {
					$template = Mage::getStoreConfig('groupdeals/notifications/email_target_met_template', $storeId);
				} elseif ($notification->getType()=='deal_over') {
					$template = Mage::getStoreConfig('groupdeals/notifications/email_deal_over_template', $storeId);
				}
				$mailTemplate = Mage::getModel('core/email_template');
				$mailTemplate->setDesignConfig(array('area' => 'frontend'))
					->setReplyTo($data['email'])
					->sendTransactional(
						$template,
						$sender , 
						$subscriber->getEmail(),
						null,
						array('data' => $postObject),
						$storeId
					);
					
				$remainingIds = array_diff($remainingIds, array($subscriberId));				
										
				if ($i>=self::ITERATIONS || $i==$unnotifiedIdsCount) {	
					$unnotifiedIdsString = implode(',', $remainingIds);							
					$notifiedIdsString = implode(',', $notifiedIds);							
					break;
				}
			}				
			
			$notification->setUnnotifiedSubscriberIds($unnotifiedIdsString);
			$notification->setNotifiedSubscriberIds($notifiedIdsString);
			if ($unnotifiedIdsString=='') {
				$notification->setStatus('complete');
			}
							
			$notification->save();	
		}
    }	
	
}