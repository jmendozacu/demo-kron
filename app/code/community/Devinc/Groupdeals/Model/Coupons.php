<?php

class Devinc_Groupdeals_Model_Coupons extends Mage_Core_Model_Abstract
{		
	const ITERATIONS = 10;
    const CSV_SEPARATOR = ',';
	
    public function _construct()
    {
        parent::_construct();
        $this->_init('groupdeals/coupons');
    }
    
    //sets coupons status to sending for each deals invoiced orders after the target has been met; this function is run via cronjob
    public function updateCoupons()
    {
		//allowed deal status array
		$statusArray = array();
		$statusArray[] = Devinc_Groupdeals_Model_Source_Status::STATUS_RUNNING;	
		$statusArray[] = Devinc_Groupdeals_Model_Source_Status::STATUS_ENDED;
		
        $active_recent_ids = Mage::getResourceModel('catalog/product_collection')
				->addAttributeToSelect('entity_id')
				->addAttributeToSelect('groupdeal_status')			
				->addAttributeToFilter('groupdeal_status', array('in', $statusArray))
				->load()->getColumnValues('entity_id');
				
		$groupdealCollection = Mage::getModel('groupdeals/groupdeals')->getCollection()->addFieldToFilter('target_met_email', 1)->addFieldToFilter('product_id', array('in', $active_recent_ids));
		
		if (count($groupdealCollection)>0) {
			$groupdealIds = array();
			foreach ($groupdealCollection as $groupdeal){
				$soldQty = Mage::getModel('groupdeals/groupdeals')->getGroupdealsSoldQty($groupdeal);
				if ($soldQty>=$groupdeal->getMinimumQty()) {
					$groupdealIds[] = $groupdeal->getId();
				}
			}
			
			if (count($groupdealIds)>0){
				$_couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('status', 'pending')->addFieldToFilter('groupdeals_id', array('in', $groupdealIds));
				if (count($_couponsCollection)>0) {		
					foreach ($_couponsCollection as $_coupon) {
				  		$_coupon->setStatus('sending')->save();
					}
				}		
			}	
		}
    }
    
    //email coupons via cronjob
	public function email()
    {			
		$_couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->setOrder('coupon_id', 'ASC')->addFieldToFilter('status', 'sending');
		if (count($_couponsCollection)>0) {				
			$i = 0;			
			foreach ($_couponsCollection as $_coupon) {
				$i++;
				
				$groupdeal = Mage::getModel('groupdeals/groupdeals')->load($_coupon->getGroupdealsId());	
				$orderItem = Mage::getModel('sales/order_item')->load($_coupon->getOrderItemId());
				if ($orderId = $orderItem->getOrderId()) {
					$order = Mage::getModel('sales/order')->load($orderId);
					$storeId = $order->getStoreId();
					$product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($groupdeal->getProductId());
					$customerName = $order->getBillingAddress()->getName();
					$sender = Mage::getStoreConfig('groupdeals/configuration/coupons_sender', $storeId);
					$replyTo = Mage::getStoreConfig('trans_email/ident_'.$sender.'/email', $storeId);
					
					//check to see if email to a friend
					if($order->getGroupdealsCouponToEmail()==''){
						$customerEmail = $order->getCustomerEmail();
					} else {
						$customerEmail = $order->getGroupdealsCouponToEmail();
					}				
					
					$emailData['name'] = $product->getName();
					$emailData['coupon_code'] = $_coupon->getCouponCode();
					$emailData['content'] = $this->getCouponHtml($groupdeal, $storeId, $_coupon, $orderItem, $customerName);	
					
					$postObject = new Varien_Object();
					$postObject->setData($emailData);	
									
					$mailTemplate = Mage::getModel('core/email_template');					
					$mailTemplate->setDesignConfig(array('area' => 'frontend'))
						->setReplyTo($replyTo)
						->sendTransactional(
							'groupdeals_notifications_email_coupon_template',
							$sender , 
							$customerEmail,
							null,
							array('data' => $postObject),
							$storeId
						);	
						
					$_coupon->setStatus('complete')->save();	
				}
								
				if ($i>=self::ITERATIONS) {							
					break;
				}
			}			
		}
    }

	//generate coupon html
	public function getCouponHtml($_groupdeal, $_storeId, $_coupon = null, $_orderItem = null, $_customerName = 'JOHN DOE')
	{
		$merchant = Mage::getModel('groupdeals/merchants')->load($_groupdeal->getMerchantId());
		$baseMediaUrl = Mage::getBaseUrl('media');
		
		//get merchant addresses
		$addresses = '';		
		if ($merchant->getAddress()!='') {
			$addressCollection = explode('_;_',$merchant->getAddress());		
			$j = 1;
			foreach ($addressCollection as $address){
				$addresses .= $j.'. '.$address.'<br/>';
				$j++;
			}
		} else {
			$addresses = '';
		}
		
		//get redeemable at/in text		
		$cities = Mage::getModel('groupdeals/crc')->getCitiesString($_groupdeal->getId(),', ');
		if ($merchant->getId() && $merchant->getStatus()==1) {
			if ($cities=='Universal') {
				$redeemableContent = Mage::helper('groupdeals')->translate(array('Redeemable at %s.', '<strong>'.Mage::getModel('license/module')->getDecodeString($merchant->getName(),$_storeId).'</strong>', $_storeId));
			} else {
				$redeemableContent = Mage::helper('groupdeals')->translate(array('Redeemable at %s in %s.', '<strong>'.Mage::getModel('license/module')->getDecodeString($merchant->getName(),$_storeId).'</strong>', '<strong>'.$cities.'</strong>'), $_storeId);			
			} 
		} else {
			if ($cities=='Universal') {
				$redeemableContent = '';
			} else {
				$redeemableContent = Mage::helper('groupdeals')->translate(array('Redeemable in %s.', '<strong>'.$cities.'</strong>'), $_storeId);
			} 			
		}
		
		//get product image
		$product = Mage::getModel('catalog/product')->setStoreId($_storeId)->load($_groupdeal->getProductId());
		if ($product->getImage()!='no_selection' && $product->getImage()){
			$productImage = Mage::helper('catalog/image')->init($product, 'image');
		} else {
			$productImage = Mage::helper('groupdeals')->getProductPlaceHolder($_storeId);
		}
		
		//load coupon/order information
		$giftContent = '';
		if(is_null($_coupon) || is_null($_orderItem)){
			$couponCode = 'LXEANN9T9T0';
			$value = Mage::helper('groupdeals')->getFormatedPrice($product, $product->getSpecialPrice(), $_storeId);
			$options = true;
			$customOptions = 'The selected custom options will appear here.';
		} else {
			$couponCode = $_coupon->getCouponCode();
			$value = Mage::helper('groupdeals')->getFormatedPrice($product, $_orderItem->getBasePriceInclTax(), $_storeId, true, false, true);
			$customOptions = '';
			$options = $this->getItemOptions($_groupdeal->getProductId(),$_orderItem);
			if ($options) {
				foreach ($options as $option) {
					$_formatedOptionValue = $this->getFormatedOptionValue($option);
					$customOptions .= '<span style="color:#333333;">'.$option['label'].':</span> '.$_formatedOptionValue['value'].'<br/>';
				}
			}
			
			$order = Mage::getModel('sales/order')->load($_orderItem->getOrderId());
			if($order->getGroupdealsCouponTo()!=''){
				$_customerName = $order->getGroupdealsCouponTo();
				$giftMessage = Mage::helper('groupdeals')->translate(array('This is a Gift Coupon sent to you by %s.', $order->getGroupdealsCouponFrom()), $_storeId);
				$giftContent = '<br/><br/><span style="width:413px; font-size:13px; color:#333333;"><strong>'.$giftMessage.'</strong></span><br/><br/><span style="font-size:13px; color:#333333;"><strong>'.Mage::helper('groupdeals')->translate(array('Gift Message:'), $_storeId).'</strong> '.$order->getGroupdealsCouponMessage().'</span>';
			}
		}
		
		//get logo
		$store = Mage::app()->getStore($_storeId);
		$package = Mage::getStoreConfig('design/package/name', $_storeId);
		$theme = Mage::getStoreConfig('design/theme/skin', $_storeId);
		$logo = Mage::getDesign()->getSkinUrl(Mage::getStoreConfig('design/header/logo_src', $_storeId), array('_area' => 'frontend', '_store' => $store, '_package' => $package, '_theme' => $theme));
		
		//get merchant logo
		$merchantLogo = '';
		if ($_groupdeal->getCouponMerchantLogo()!='' && $merchant->getId() && $merchant->getStatus()==1 && $merchant->getMerchantLogo()!='') {
			$merchantLogo = '<img style="width:100px;float:right;margin:10px 0px 0 0;" src="'.$baseMediaUrl.'/'.$merchant->getMerchantLogo().'" alt="merchant_logo" />';	
		}
		
		$couponExpirationDate = $_groupdeal->getCouponExpirationDate();
		
		$groupdealFinePrint = str_replace('<ul>','<ul style="padding-left:18px; margin-left:0px; list-style-type:disc;">',str_replace('<ol>','<ol style="padding-left:18px; margin-left:0px; list-style-type:decimal;">',$product->getGroupdealFineprint()));
		$groupdealHighlights = str_replace('<ul>','<ul style="padding-left:18px; margin-left:0px; list-style-type:disc;">',str_replace('<ol>','<ol style="padding-left:18px; margin-left:0px; list-style-type:decimal;">',$product->getGroupdealHighlights()));
		
		//set coupon width
		$tableWidth = 700;
		$imageWidth = 295;
		if (Mage::helper('core')->isModuleEnabled('Devinc_Gdtheme') && Mage::getSingleton('core/design_package')->getTheme()=='groupdeals') {
			$tableWidth = 645;
			$imageWidth = 280;
		}
		
		$html = 
		   '<html><body style="font-family:Arial;">
			<table width="'.$tableWidth.'" style="background:#F0F1F3; padding:10px; font-size:11px;">
				<thead>
				    <tr  style="background:#313131;">
				    	<th style="padding:20px 25px; text-align:left; border-bottom:2px solid #F1461E;">
				    		<img width="150px" style="float:left;" src="'.$logo.'" alt="logo" />'.$merchantLogo.'
				    	</th>
				    </tr>
				</thead>
				<tbody>
				    <tr style="background:#FFFFFF;">
				    	<td style="padding:15px; color:#808080; ">
				    		<div style="text-align:left; clear:both; margin:0 0 10px 0;">
				    			<h3 style="padding:0px; font-size:22px; font-weight:bold; margin:0px; color:#333333;">'.$product->getName().'</h3> 
				    			<span style="width:413px; font-size:13px; color:#333333; ">'.$redeemableContent.'</span>
				    			'.$giftContent.'
				    		</div>	
				    		<div style="float:left; width:46%;"><img width="'.$imageWidth.'px" style="border:2px solid #D6D7D9;" src="'.$productImage.'" alt="deal_image" /></div>
				    		<div style="float:right; width:50%;">	
				    			<div style="margin-top:10px; clear:both; margin:0 0 3px; padding:0 0 0 2px;">
				    				<span style="font-size:12px; color:#333333;">'.Mage::helper('groupdeals')->translate(array('Customer Name:'), $_storeId).' <span style="font-weight:bold;">'.$_customerName.'</span></span><br/>
				    			</div>
				    			<table width="100%" style="margin:0px; font-size:11px; border-top:1px dashed #DDDDDD; padding-bottom:5px;">
				    				<tr><td style="color:#333333; padding:0; width:70px; padding-top:4px;">'.Mage::helper('groupdeals')->translate(array('Coupon #:'), $_storeId).'</td> <td style="padding:0px; padding-top:4px;">'.$couponCode.'</td></tr>';
				    				
				    			if ($couponExpirationDate!='') $html .= '<tr><td style="color:#333333; padding:0px;">'.Mage::helper('groupdeals')->translate(array('Valid Until:'), $_storeId).'</td> <td style="padding:0;">'.date("M d, Y", strtotime($couponExpirationDate)).' </td></tr>';
				    			
				    			if ($_groupdeal->getCouponPrice()==1) $html .= '<tr><td style="color:#333333; ">'.Mage::helper('groupdeals')->translate(array('Value:'), $_storeId).'</td> <td style="padding:0px;">'.$value.'</td></tr>';
				    			
				    			if ($options) $html .= '<tr><td colspan="2" style="padding:5px 0 0;"><span style="font-size:11px; color:#333333">'.Mage::helper('groupdeals')->translate(array('CUSTOM OPTIONS:'), $_storeId).'</span><br/>'.$customOptions.'</td></tr>';	
				    			
				    			if ($_groupdeal->getCouponBarcode()!='') $html .= '<tr><td colspan="2"><img style="width:110px; margin-top:3px;" src="'.$baseMediaUrl.'/'.$_groupdeal->getCouponBarcode().'" alt="barcode" /></td></tr>';	
				    			
				    			$html .= '</table>';
				    			if ($merchant->getId() && $merchant->getStatus()==1){ 
				    				$html .='<div style=" padding:12px 0 0 0px; border-top:1px dashed #DDDDDD;">';
				    				
				    				if ($_groupdeal->getCouponMerchantAddress()==1 && $addresses!='') {
				    					$html .= '<span style="color:#333333">'.Mage::helper('groupdeals')->translate(array('ADDRESS:'), $_storeId).'</span><br/>'.$addresses.'<br/>';
				    				} elseif ($_groupdeal->getCouponMerchantAddress()==1) {
				    					$html .= '<span style="color:#333333">'.Mage::helper('groupdeals')->translate(array('ADDRESS:'), $_storeId).'</span><br/>'.Mage::getModel('license/module')->getDecodeString($merchant->getRedeem(), $_storeId).'<br/><br/>';
				    				}
				    				
				    				if ($_groupdeal->getCouponMerchantContact()==1) {
				    					$html .= '<table width="100%" style="font-size:11px; margin:0px; padding:10px 0px 3px 0px; border-top:1px dashed #DDDDDD; " cellspacing="0" cellpadding="0">
				    					<tr><td colspan="2"><span style="font-size:11px; color:#333333">'.Mage::helper('groupdeals')->translate(array('CONTACT INFO:'), $_storeId).'</span></td></tr>';
				    					
				    					if (Mage::getModel('license/module')->getDecodeString($merchant->getPhone(),$_storeId)!='') $html .= '<tr><td style="color:#333333; padding:1px 0px; width:50px;"><span style="color:#333333;">'.Mage::helper('groupdeals')->translate(array('Phone:'), $_storeId).'</span></td> <td style="padding:1px 0px;">'.Mage::getModel('license/module')->getDecodeString($merchant->getPhone(),$_storeId).'</td></tr>';
				    					
				    					if (Mage::getModel('license/module')->getDecodeString($merchant->getMobile(),$_storeId)!='') $html .= '<tr><td style="color:#333333; padding:1px 0px; width:50px;"><span style="color:#333333;">'.Mage::helper('groupdeals')->translate(array('Mobile:'), $_storeId).'</span></td> <td style="padding:1px 0px;">'.Mage::getModel('license/module')->getDecodeString($merchant->getMobile(),$_storeId).'</td></tr>';
				    					
				    					if (Mage::getModel('license/module')->getDecodeString($merchant->getEmail(),$_storeId)!='') $html .= '<tr><td style="color:#333333; padding:1px 0px; width:50px;"><span style="color:#333333;">'.Mage::helper('groupdeals')->translate(array('E-Mail:'), $_storeId).'</span></td> <td style="padding:1px 0px;">'.Mage::getModel('license/module')->getDecodeString($merchant->getEmail(),$_storeId).'</td></tr>';
				    					
				    					if (Mage::getModel('license/module')->getDecodeString($merchant->getWebsite(),$_storeId)!='') $html .= '<tr><td style="color:#333333; padding:1px 0px; width:50px;"><span style="color:#333333;">'.Mage::helper('groupdeals')->translate(array('Website:'), $_storeId).'</span></td> <td style="padding:1px 0px;">'.Mage::getModel('license/module')->getDecodeString($merchant->getWebsite(),$_storeId).'</td></tr>';
				    					
				    					$html .= '</table>';
				    				}
				    				$html .= '</div>';
				    			}
				    		$html .= '</div>';
				    		
				    		if ($_groupdeal->getCouponFinePrint()==1 || $_groupdeal->getCouponHighlights()==1) {
				    			$html .= '<div style="float:left; width:100%; border-top:1px dashed #DDDDDD; margin:8px 0 0; padding:10px 0 0;">';
				    			if ($_groupdeal->getCouponFinePrint()==1) $html .= '<div style="float:left; width:48%"><h3 style="display:block; padding:0px; font-size:14px; font-weight:bold; margin:0; color:#333333;">'.Mage::helper('groupdeals')->translate(array('The Fine Print'), $_storeId).'</h3>'.$groupdealFinePrint.'</div>';
				    				
				    			if ($_groupdeal->getCouponHighlights()==1) $html .= '<div style="float:right; width:50%"><h3 style="display:block; padding:0px; font-size:14px; font-weight:bold; margin:0; color:#333333;">'.Mage::helper('groupdeals')->translate(array('Highlights'), $_storeId).'</h3>'.$groupdealHighlights.'</div>';
				    				
				    			$html .= '</div>';
				    		}
				    		
				    		if (($_groupdeal->getCouponMerchantDescription()==1 || $_groupdeal->getCouponBusinessHours()==1) && $merchant->getId() && $merchant->getStatus()==1) {
				    		$html .= '<div style="float:left; width:100%">';
				    		
				    			if ($_groupdeal->getCouponMerchantDescription()==1) $html .= '<h3 style="display:block; padding:0px; font-size:14px; font-weight:bold; margin:10px 0 0; color:#333333;">'.Mage::helper('groupdeals')->translate(array('Merchant Description'), $_storeId).'</h3>'.Mage::getModel('license/module')->getDecodeString($merchant->getDescription(),$_storeId);
				    			$businessHours = Mage::getModel('license/module')->getDecodeString($merchant->getBusinessHours(),$_storeId);
				    			if ($_groupdeal->getCouponBusinessHours()==1 && $businessHours!='') $html .= '<span style="display:block; padding:0px; font-size:11px; font-weight:bold; margin:10px 0px 0; color:#666666;">'.Mage::helper('groupdeals')->translate(array('BUSINESS HOURS'), $_storeId).'</span><div style="float:left; width:75%;">'.$businessHours.'</div>';
				    							    		
				    		$html .= '</div>';	
				    		}
				    				
				    		if ($_groupdeal->getCouponAdditionalInfo()!='') $html .= '<div style="float:left; width:100%"><h3 style="display:block; padding:0px; font-size:14px; font-weight:bold; margin:10px 0 0; color:#333333;">'.Mage::helper('groupdeals')->translate(array('Additional Info'), $_storeId).'</h3>'.$_groupdeal->getCouponAdditionalInfo().' </div>';
				    		
				    	$html .= '</td> 
				    </tr>
				</tbody>
			</table>
			</body>
			</html>';
			
		return $html;
	}
	
    public function getItemOptions($product_id, $_orderItem_id)
    {
		$options = $_orderItem_id->getProductOptions();
        $result = array();
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }
	
	public function getFormatedOptionValue($optionValue)
    {
        $optionInfo = array();

        // define input data format
        if (is_array($optionValue)) {
            if (isset($optionValue['option_id'])) {
                $optionInfo = $optionValue;
                if (isset($optionInfo['value'])) {
                    $optionValue = $optionInfo['value'];
                }
            } elseif (isset($optionValue['value'])) {
                $optionValue = $optionValue['value'];
            }
        }

        // render customized option view
        if (isset($optionInfo['custom_view']) && $optionInfo['custom_view']) {
            $_default = array('value' => $optionValue);
            if (isset($optionInfo['option_type'])) {
                try {
                    $group = Mage::getModel('catalog/product_option')->groupFactory($optionInfo['option_type']);
                    return array('value' => $group->getCustomizedView($optionInfo));
                } catch (Exception $e) {
                    return $_default;
                }
            }
            return $_default;
        }

        // truncate standard view
        $result = array();
        if (is_array($optionValue)) {
            $_truncatedValue = implode("\n", $optionValue);
            $_truncatedValue = nl2br($_truncatedValue);
            return array('value' => $_truncatedValue);
        } else {
            $_truncatedValue = Mage::helper('core/string')->truncate($optionValue, 55, '');
            $_truncatedValue = nl2br($_truncatedValue);
        }

        $result = array('value' => $_truncatedValue);

        if (Mage::helper('core/string')->strlen($optionValue) > 55) {
            //$result['value'] = $result['value'] . ' <a href="#" class="dots" onclick="return false">...</a>';
            $optionValue = nl2br($optionValue);
            $result = array_merge($result, array('full_view' => $optionValue));
        }

        return $result;
    }
}