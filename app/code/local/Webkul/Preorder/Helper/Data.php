<?php

	class Webkul_Preorder_Helper_Data extends Mage_Core_Helper_Abstract {

		public function getCurrentStoreId() {
			$storeId = Mage::app()->getStore()->getStoreId();
			return $storeId;
		}

		public function getPayPreorderHtml($mianProduct) {
			$preorderType = $this->getPreorderType();
			$percentAccept=$this->getPreorderPercent();
			$productType = $mianProduct->getTypeId();
			$html="";
			$finalprice=0;
			if($productType == "bundle") {
				$selectionCollection = $mianProduct->getTypeInstance(true)->getSelectionsCollection($mianProduct->getTypeInstance(true)->getOptionsIds($mianProduct), $mianProduct);
				$selections=$selectionCollection->getData();
				$childrenProduct=$mianProduct->getTypeInstance(true)->getChildrenIds($mianProduct->getId(), false);
				$maxPrice=0;
				$minPrice=0;
				foreach ($childrenProduct[0] as $productId) {
					$selectionQty=1;
					$product=Mage::getModel('catalog/product')->load($productId);
					$regularprice=$product->getPrice();
					$specialprice=$product->getSpecialPrice();
					if($this->isInOffer($product)) {
						$finalprice=$specialprice;
					} else {
						$finalprice=$regularprice;
					}

					foreach ($selections as $selection) {
						if($selection['entity_id']==$productId && $selection['selection_qty']>0)
							$selectionQty=$selection['selection_qty'];
					}
					if($this->isPreorder($productId)) {
						$finalprice=(($finalprice*intval($selectionQty))*$percentAccept)/100;
					}

					$maxPrice=$maxPrice+$finalprice;
					if($minPrice==0) {
						$minPrice=$finalprice;
					} elseif ($minPrice>$finalprice) {
						$minPrice=$finalprice;
					}
				}
				$html.="<div class='price-box wp_price'>";
				$html.="<p class='price-from wk_price'>";
				$html.="span class='price-label'>From:</span> ";
				$html.=Mage::helper("core")->currency($minPrice);
				$html.="</p>";
				$html.="<p class='price-to wk_price'>";
				$html.="<span class='price-label' style='color:#2f2f2f'>To:</span> ";
				$html.=Mage::helper("core")->currency($maxPrice);
				$html.="</p>";
				$html.="<p style='color:green'>Price with Preorder</p>";
				$html.="</div>";
				return $html;
			}

			if($productType == "grouped") {
				$childrenProduct=$mianProduct->getTypeInstance(true)->getChildrenIds($mianProduct->getId(), false);
				foreach ($childrenProduct as $value) {
					foreach ($value as $key => $productId) {
						$product=Mage::getModel('catalog/product')->load($productId);
						$regularprice=$product->getPrice();
						$specialprice=$product->getSpecialPrice();
						if($this->isInOffer($product)) {
							$finalprice=$specialprice;
						} else {
							$finalprice=$regularprice;
						}
						if($this->isPreorder($productId)) {
							$finalprice=($finalprice*$percentAccept)/100;
						}
						if($minPrice==0){
							$minPrice=$finalprice;
						} elseif($minPrice>$finalprice) {
							$minPrice=$finalprice;
						}
					}
				}
				
				$html.="<div style='width:100%' class='wp_price'>";
				$html.="<p style='color:green'><b style='color:#2f2f2f'>Starting at:</b>";
				$html.=Mage::helper("core")->currency($minPrice);
				$html.=" with Preorder</p>";
				$html.="</div>";
			
			} else {
				$regularprice=($mianProduct->getPrice()*$percentAccept)/100;
				$specialprice=($mianProduct->getSpecialPrice()*$percentAccept)/100;
				
				if($this->isInOffer($mianProduct)) {
					$finalprice=$specialprice;
					$price = $mianProduct->getSpecialPrice();
				} else {
					$finalprice=$regularprice;
					$price = $mianProduct->getPrice();
				}
				if($preorderType!=1){
					$finalprice = $price;
				}
				
				$html.='<div class="price-box wp_price">';
				$html.='<span class="regular-price"><span class="price">';
				$html.='just pay '.Mage::helper("core")->currency($finalprice).' as preorder</span></span></div>';
			}
			return $html;
		}

		public function getAdditionalMessage($productId, $price='') {
			$preorderType = $this->getPreorderType();
			$percentAccept=$this->getPreorderPercent();
			if($price=="") {
				$price = $this->getPrice($productId);
			}

			$price = $percentAccept*$price/100;
			$price = Mage::helper("core")->currency($price);
			if($preorderType == 1 && $percentAccept != "") {
				$html = "<div class='wk-additional-msg'>";
				$html.="<span class='wk-add-title'>Final Price: </span>";
				$html.="<span class='wk-add-content'>".$percentAccept."% of the configured price. See final price in cart.</span>";
				// $html.="<span class='wk-add-content'></span>";
				$html.="</div>";
			}
			
			return $html;
		}
		public function getPriceHtml($product) {
			$percentAccept=$this->getPreorderPercent();
			$html="";
			$regularprice=($product->getPrice()*$percentAccept)/100;
			$specialprice=($product->getSpecialPrice()*$percentAccept)/100;
			
			if($this->isInOffer($product)) {
				$html.="<div class='price-box wp_price'>";
				$html.="<p class='old-price'>";
				$html.="<span class='price-label'>Regular Price:</span>";
				$html.="<span class='price'>";
				$html.=Mage::helper('core')->currency($regularprice, true, false);
				$html.="</span>";
				$html.="</p>";
				$html.="<p class='special-price'>";
				$html.="<span class='price-label'>Special Price:</span>";
				$html.="<span id='product-price-".$product->getEntityId()."' class='price'>".Mage::helper('core')->currency($specialprice, true, false);
				$html.="</span>";
				$html.="</p>";
				$html.="</div>";
			} else {
				$html.="<div class='price-box wp_price'>";
				$html.="<span id='product-price-".$product->getEntityId()."' class='regular-price'>";
				$html.="<span class='price'>".Mage::helper('core')->currency($regularprice, true, false)."</span>";
				$html.="</span>";
				$html.="</div>";
			}
			return $html;
		}

		public function getArrayFromString($str, $seperator=',') {
			$array= array();
			$pos = strpos($str, $seperator);
			if(strlen($str)>0) {
				if($pos!=false) {
					$arr =  explode(",", $str);
					foreach ($arr as $key => $value) {
						$value = trim($value);
						if($value!="") {
							$array[] = $value;
						}
					}
				} else {
					$array[] = $str;
				}
			}
			$array = array_unique($array);
			return($array);
		}
		public function getPreorderCustomMessageHtml() {
			$html="";
			$storeId = $this->getCurrentStoreId();
			$customMessage = $this->getCustomMessage();
			$html.="<div class='wk-prodrder-custom-message'>".$customMessage."</div>";
			return $html;
		}

        /**
         * @param $productId
         * @return bool
         */
		public function isPreorder($productId) {

			if($productId=="" || $productId==0) {
				return false;
			}
			
			$product = Mage::getModel("catalog/product")->load($productId);
			$productType = $product->getTypeID();
			if(in_array($productType, array('configurable','bundle','grouped'))) {
				return false;
			}
			$status = 0;
            $stockStatus = $this->getStockStatus($productId);
			
			if($stockStatus!=1) {
				$preorderType = $this->getPreorderType();
				$preorderAction = $this->getPreorderAction();
				
				if($preorderAction==1) {
					$status = 1;
				} elseif($preorderAction==2) {
					$filterProduct = $this->getFilterProducts(1);
					$filterProductArray = $this->getArrayFromString($filterProduct);
					if(in_array($productId, $filterProductArray)) {
						$status = 1;
					}
				} elseif($preorderAction==3) {
					$filterProduct = $this->getFilterProducts(2);
					$filterProductArray = $this->getArrayFromString($filterProduct);
					if(!in_array($productId, $filterProductArray)) {
						$status = 1;
					}
				} else {
					$_product = Mage::getModel('catalog/product')->load($productId);
					$attr = $_product->getResource()->getAttribute('wk_preorder');
					$attrId = $attr->getSource()->getOptionId('Enable');

					if($_product->getdata('wk_preorder')==$attrId) {
						$status=1;
					}
				}
				if($status==1) {
					return true;
					// return "Preorder";
				} else {
					return false;
					// return "Normal Order";
				}
			} else {
				return false;
				// return "Normal Order";
			}
		}

		public function getPreorderType() {
			$storeId = $this->getCurrentStoreId();
			return Mage::getStoreConfig("preorder/preorder/preorderStatus", $storeId);
		}

		public function getPreorderAction() {
			$storeId = $this->getCurrentStoreId();
			return Mage::getStoreConfig("preorder/preorder/preorderAction", $storeId);
		}

		public function getPreorderPercent($productId=0) {
			$storeId = $this->getCurrentStoreId();
			if($productId>0) {
				$product = Mage::getModel("catalog/product")->load($productId);
				$preorderPercent = $product->getWkPreorderPercent();
				if($preorderPercent>=0 && $preorderPercent!="") {
					return $preorderPercent;
				} else {
					return Mage::getStoreConfig("preorder/preorder/percent", $storeId);
				}
			} else {
				return Mage::getStoreConfig("preorder/preorder/percent", $storeId);
			}
		}

		public function getCustomMessage() {
			$storeId = $this->getCurrentStoreId();
			return Mage::getStoreConfig("preorder/preorder/custom_message", $storeId);
		}

		public function getFilterProducts($type=2) {
			$storeId = $this->getCurrentStoreId();
			if($type==1) {
				return Mage::getStoreConfig('preorder/preorder/fewProducts', $storeId);		
			} else {
				return Mage::getStoreConfig('preorder/preorder/manyProducts', $storeId);
			}
			
		}

		public function getEmailAction() {
			$storeId = $this->getCurrentStoreId();
			return Mage::getStoreConfig('preorder/preorder/preorderMail', $storeId);
		}

		public function getAdminEmail() {
			$storeId = $this->getCurrentStoreId();
			return Mage::getStoreConfig("preorder/preorder/adminEmail", $storeId);
		}

		public function getStockStatus($productId) {
			$stockStatus = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId)->getIsInStock();
			return $stockStatus;
		}

		public function isAutoEmail() {
			$emailAction = $this->getEmailAction();
			if($emailAction==0){
				return true;
			} else {
				return false;
			}
		}
		public function isInOffer($product) {
			$specialPrice = number_format($product->getFinalPrice(), 2);
			$regularPrice = number_format($product->getPrice(), 2);

			if ($specialPrice != $regularPrice) {
				return $this->chekOffer($product->getData('special_from_date'), $product->getData('special_to_date'));
			} else {
				return false;
			}
		}

		protected function chekOffer($fromDate, $toDate) {
			if($fromDate) {
				$fromDate = strtotime($fromDate);
				$toDate = strtotime($toDate);
				$now = strtotime(Mage::app()->getLocale()->date()->setTime('00:00:00')->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
				if ($toDate) {
					if ($fromDate <= $now && $now <= $toDate){
						return true;
					}
				} else {
					if ($fromDate <= $now) {
						return true;
					}
				}
			}
			return false;
		}
		function getCode() {
			$str="";
			$string = strtotime(date('y-m-d h:i:s'));
			$array = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
			$random_keys=array_rand($array,10);
			foreach ($random_keys as $key => $value) {
				$str.=$array[$value];
			}
			for($i=0;$i<strlen($string);$i++) {
				$temp = substr($string, $i, 1);
				$str.= $array[$temp];
			}
			return($str);
		}
		
		function getCharacterArray() {
			$array = array();
			for($i=65;$i<=90;$i++){
				$array[] = "&#".$i;
			}
			for($i=97;$i<=122;$i++){
				$array[] = "&#".$i;
			}
			for($i=48;$i<=57;$i++){
				$array[] = "&#".$i;
			}
			return($array);
		}
		public function sendEmail($array, $product) {
			$productName = $product->getName();
			$adminEmail = $this->getAdminEmail();
			$adminName = "Admin";
			$loginUrl = Mage::getUrl('customer/account/login');
			// $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
			if(count($array)>0) {
				$email_template = Mage::getModel("core/email_template")->loadDefault("notification_mail");
				$template_variable = array();

				$email_template->setSenderName($adminName);
				$email_template->setSenderEmail($adminEmail);

				foreach ($array as $customerId) {
					$customer = Mage::getModel('customer/customer')->load($customerId);
					$template_variable["customer_name"] = $customer->getFirstname();
					// $template_variable["customer_email"] = $customer->getEmail();
					// $template_variable["product_name"] = $productName;

					$template_variable["msg"] = "Product '".$productName."' is in Stock.<br> Please <a href='".$loginUrl."'>go to your account</a> to complete preorder";

					$email_template->getProcessedTemplate($template_variable);
					$email_template->send($customer->getEmail(),$customer->getFirstname(),$template_variable);
					// $email_template->send("rahul@webkul.com","Rahul Mahto",$template_variable);
				}
			}
		}

		public function sendEmailFromAdmin($customerArray, $productArray, $preOrderIdArray) {
			$loginUrl = Mage::getUrl('customer/account/login');
			$model = Mage::getModel("preorder/preorder");
			$adminEmail = $this->getAdminEmail();
			$adminName = "Admin";
			if(count($customerArray)>0) {
				$email_template = Mage::getModel("core/email_template")->loadDefault("notification_mail");
				$template_variable = array();
				
				$email_template->setSenderName($adminName);
				$email_template->setSenderEmail($adminEmail);

				foreach ($customerArray as $key => $customerId) {
					if($customerId>0) {
						$product = Mage::getModel("catalog/product")->load($productArray[$key]);
						$productName = $product->getName();
						$customer = Mage::getModel('customer/customer')->load($customerId);
						$template_variable["customer_name"] = $customer->getFirstname();

						// $template_variable["customer_email"] = $customer->getEmail();
						// $template_variable["product_name"] = $productName;
						$template_variable["msg"] = "Product '".$productName."' is in Stock.<br> Please <a href='".$loginUrl."'>go to your account</a> to complete preorder";
						// $template_variable["msg"] = "Product '".$productName."' is in Stock. Please go to your account to complete preorder";
						$email_template->getProcessedTemplate($template_variable);
						$email_template->send($customer->getEmail(),$customer->getFirstname(),$template_variable);
						// $email_template->send("rahul@webkul.com","Rahul Mahto",$template_variable);

						$model->load($preOrderIdArray[$key]);
						$data = array("notify"=>1);
						$model->addData($data);
						$model->setId($preOrderIdArray[$key]);
						$model->save();
					}
				}
			}
		}

		public function notifyStatus($array) {
			$model = Mage::getModel("preorder/preorder");
			if(count($array)>0) {
				foreach ($array as $preorderId) {
					$data = array('notify'=>1);
					$model->load($preorderId)
						  ->addData($data)
						  ->setId($preorderId)
						  ->save();
				}
			}
		}

		public function isPreorderCompleteOrder() {
			$str = Mage::getSingleton("core/session")->getPreorderReferenceNumber();
			$ref = explode("&", $str);
			$refNumber = 0;
			$preOrderItemId = 0;
			if(isset($ref[0])){
				$refNumber = $ref[0];
			}
			if(isset($ref[1])){
				$preOrderItemId = $ref[1];
			}

			if($refNumber!="") {
				$quote = Mage::getSingleton('checkout/session')->getQuote();
				$items = $quote->getAllVisibleItems();
				foreach ($items as $item) {
					$itemId = $item->getItemId();
				}
				if($itemId==$preOrderItemId) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function emptyOtherItems() {
			$str = Mage::getSingleton("core/session")->getPreorderReferenceNumber();
			$ref = explode("&", $str);
			$refNumber = $ref[0];
			$preOrderItemId = $ref[1];
			if($refNumber!="") {
				$cartHelper = Mage::helper('checkout/cart');
				$items = $cartHelper->getCart()->getItems();
				foreach ($items as $item) {
					$itemId = $item->getItemId();
				}
				if($itemId==$preOrderItemId) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		
		public function getPrice($productId) {
			$product = Mage::getModel('catalog/product')->load($productId);

			$regularprice=$product->getPrice();
			$specialprice=$product->getSpecialPrice();
			
			if($this->isInOffer($product)) {
				$finalprice=$specialprice;
			} else {
				$finalprice=$regularprice;
			}
			return $finalprice;
		}

		public function isPreorderInCart() {
			$cartHelper = Mage::helper('checkout/cart');
			$cartItemsQty = $cartHelper->getItemsCount();
			$items = $cartHelper->getCart()->getItems();
		}

		public function isBundleProductInCart() {
			$bundleProductNumber = 0;
			$configurableProductNumber = 0;
			$cartHelper = Mage::helper('checkout/cart');
			$cartItemsQty = $cartHelper->getItemsCount();
			$items = $cartHelper->getCart()->getItems();
			foreach ($items as $item) {
				$itemId = $item->getItemId();
				$currentProductId = $item->getProductId();
				$pro = Mage::getModel("catalog/product")->load($currentProductId);
				$productType = $pro->getTypeID();
				if($productType=="bundle") {
					$bundleProductNumber++;
				}
				if($productType=="configurable") {
					$configurableProductNumber++;
				}
			}
			return $bundleProductNumber;
		}
		public function lastItemIdOfCart() {
			$collection = Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection();
			$collection->getSelect()->order('created_at DESC')->limit(1);
			$data = $collection->getData();
			$itemId = $data[0]['item_id'];
			return $itemId;
		}

		public function isBundlePreorder($cartItemId) {
			$flag=0;
			$cartHelper = Mage::helper('checkout/cart');
			$cartItemsQty = $cartHelper->getItemsCount();
			$items = $cartHelper->getCart()->getItems();

			foreach ($items as $item) {
				$itemId = $item->getItemId();
				$parentItemId = $item->getParentItemId();

				$currentProductId = $item->getProductId();
				if($parentItemId==$cartItemId) {
					if($this->isPreOrder($currentProductId)) {
						$flag=1;
					}
				}
			}
			if($flag==1) {
				return true;
			} else {
				return false;
			}
		}

		public function isGroupedPreorder($productId) {
			$flag=0;
			$product = Mage::getModel("catalog/product")->load($productId);
			$productType = $product->getTypeId();
			if($productType=="grouped") {
				$items = $product->getTypeInstance(true)->getAssociatedProducts($product);
				foreach ($items as $item) {
					$id = $item->getEntityId();
					if($this->isPreorder($id)){
						$flag = 1;
					}
				}
				if($flag==1){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function getPreordetAmontHtml($productId,$amountPrice=0) {
			if($amountPrice>0) {
				$price = $amountPrice;
			} else {
				$price = $this->getPrice($productId);
			}
			
			$percentAccept=$this->getPreorderPercent();
			$paidAmount = $percentAccept*$price/100;
			$html = "";
			$html.='<div class="price-box wp_price">';
			$html.='<span class="regular-price"><span class="price">';
			$html.='just pay '.Mage::helper("core")->currency($paidAmount).' as preorder</span></span></div>';
			return $html;
		}

		public function getPreordetAmountHtml($productId, $amountPrice = 0) {
			if($amountPrice > 0) {
				$price = $amountPrice;
			} else {
				$price = $this->getPrice($productId);
			}
			$percentAccept = $this->getPreorderPercent();
			$preorderType = $this->getPreorderType();
			if($preorderType == 1) {
				$paidAmount = $percentAccept*$price/100;
			} else {
				$paidAmount = $price;
			}
			$html = "";
			$html.='<div class="price-box wp_price">';
			$html.='<span class="regular-price"><span class="price">';
			$html.='just pay '.Mage::helper("core")->currency($paidAmount).' as preorder</span></span></div>';
			return $html;
		}

		public function getPreorderOrder() {
			$str = Mage::getSingleton("core/session")->getPreorderOrderNumber();
			$ref = explode("&", $str);
			$preOrderId = $ref[0];
			$orderId = $ref[1];

			if($orderId>0) {
				$order = Mage::getModel('sales/order')->load($orderId);
				return $order;
			} else {
				return '';
			}

		}

		public function sendCompletePreOrderEmail($order) {
			$order = $this->getPreorderOrder();
			$storeId = $order->getStore()->getId();
			if (!Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
				return $order;
			}
			// Get the destination email addresses to send copies to
			
			// Start store emulation process
			$appEmulation = Mage::getSingleton('core/app_emulation');
			$initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);
			try {
				// Retrieve specified view block from appropriate design package (depends on emulated store)
				$paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
					->setIsSecureMode(true);
				$paymentBlock->getMethod()->setStore($storeId);
				$paymentBlockHtml = $paymentBlock->toHtml();
			} catch (Exception $exception) {
				// Stop store emulation process
				$appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
				throw $exception;
			}
			// Stop store emulation process
			$appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
			// Retrieve corresponding email template id and customer name
			if ($order->getCustomerIsGuest()) {
				$templateId = "preorder_complete_guest_email";	
				$customerName = $order->getBillingAddress()->getName();
			} else {
				$templateId = "preorder_complete_email";
				$customerName = $order->getCustomerName();
			}
			$mailer = Mage::getModel('core/email_template_mailer');
			$emailInfo = Mage::getModel('core/email_info');
			$emailInfo->addTo($order->getCustomerEmail(), $customerName);
			
			$mailer->addEmailInfo($emailInfo);
			// Email copies are sent as separated emails if their copy method is 'copy'
			
			// Set all required params and send emails
			$mailer->setSender(Mage::getStoreConfig('sales_email/order/identity', $storeId));
			$mailer->setStoreId($storeId);
			$mailer->setTemplateId($templateId);
			$mailer->setTemplateParams(array(
				'order'			=> $order,
				'billing'		=> $order->getBillingAddress(),
				'payment_html'	=> $paymentBlockHtml
				)
			);			
			$mailer->send();
			$order->setEmailSent(true);
			// $order->_getResource()->saveAttribute($order, 'email_sent');
			// return $order;
		}


		public function isBundleProductPreOrder($productId) {
			$flag=0;
			$product=Mage::getModel('catalog/product')->load($productId);
			$selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product);
			$selections=$selectionCollection->getData();
			$childrenProduct=$product->getTypeInstance(true)->getChildrenIds($productId, false);
			
			foreach ($childrenProduct[0] as $childProductId) {
				if($this->isPreorder($childProductId)) {
					$flag=1;
					break;
				}
			}
			if($flag==1) {
				return true;
			} else {
				return false;
			}
		}

		public function isGroupedProductPreOrder($productId) {
			$flag=0;
			$product=Mage::getModel('catalog/product')->load($productId);
			$childrenProduct=$product->getTypeInstance(true)->getChildrenIds($product->getId(), false);
			foreach ($childrenProduct as $value) {
				foreach ($value as $key => $childProductId) {
					if($this->isPreorder($childProductId)) {
						$flag=1;
						break;
					}
				}
			}
			if($flag==1) {
				return true;
			} else {
				return false;
			}
		}

		public function isConfigurableProductPreOrder($productId) {
			$flag=0;
			$product=Mage::getModel('catalog/product')->load($productId);
			$conf = Mage::getModel('catalog/product_type_configurable')->setProduct($product);
			$associatedProducts = $conf->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();
			foreach($associatedProducts as $childProduct) {
				$childProductId = $childProduct->getId();
				if($this->isPreorder($childProductId)) {
					$flag=1;
					break;
				}
			}
			if($flag==1) {
				return true;
			} else {
				return false;
			}
		}

		public function getPreorderCompleteStatus($orderId) {
			$preOrderDetails = array('preorder_token'=>"", 'preorder_id'=>"",'unique_number'=>"", 'order_type'=>0);
			$flag = 0;
			$type = 0;
			$preOrderIdArray = array();
			$notifyArray = array();
			$productIdArray = array();
			$childProductIdArray = array();
			$model = Mage::getModel("preorder/preorder");
			$collection = $model->getCollection()->addFieldToFilter("orderid",$orderId)->addFieldToFilter("status",1);
			if($collection) {
				foreach ($collection as $item) {
					$type = 1;

					$preorderId = $item->getPreorderId();
					$productId = $item->getItemid();
					$childProductId = $item->getChildid();
					$refNumber = $item->getRefNumber();
					$qty = $item->getQty();

					$notify = $item->getNotify();
					$timeStamp = $item->getTime();

					$notifyArray[] = $notify;
					$productIdArray[] = $productId;
					$childProductIdArray[] = $childProductId;
					$preOrderIdArray[] = $preorderId;
				}
			}
			$childProductStatus = 1;
			$productStatus = 0;
			if(count($preOrderIdArray)==1) {
				$preOrderDetails['order_type'] = $type;
				if($notifyArray[0]==1) {
					$preOrderDetails['preorder_token'] = $refNumber;
					$preOrderDetails['preorder_id'] = $preorderId;
					$preOrderDetails['unique_number'] = $timeStamp;
					
					if(count($childProductIdArray)>0) {
						$stockStatus = $this->getStockStatus($childProductIdArray[0]);
						if($stockStatus!=1) {
							$childProductStatus = 0;
						}
					}
					$stockStatus = $this->getStockStatus($productIdArray[0]);
					if($stockStatus==1) {
						$productStatus = 1;
					}
					if($productStatus==1 && $childProductStatus==1) {
						$flag = 1;
					}
					
				}
			} else { //bundle product case
				

			}
			if($flag==1) {
				$preOrderDetails['notify'] = 1;
				return $preOrderDetails;
			} else {
				$preOrderDetails['notify'] = 0;
				return $preOrderDetails;
			}
		}
	}