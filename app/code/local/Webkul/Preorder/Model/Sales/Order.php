<?php

	class Webkul_Preorder_Model_Sales_Order extends Mage_Sales_Model_Order {

		public function sendNewOrderEmail() {
			$helper = Mage::helper("preorder");
			$order = $this;
			$flag = 0;
			$isPreorderComplete = 0;
			$incrementId = $order->getIncrementId();
			$preorderFlag = 0;
			$preorderCompleteRef = Mage::getSingleton("core/session")->getPreorderCompleteStatus();
			$temp = explode("=", $preorderCompleteRef);
			$completeStatusId = trim($temp[1]);
			if($completeStatusId==$incrementId) {
				$isPreorderComplete=1;
			}
			foreach ($order->getAllItems() as $item) {
				$typeArray = array('bundle','configurable');
				$parentId = $item->getParentItemId();
				$productId = $item->getProductId();
				$_product = Mage::getModel('catalog/product')->load($productId);
				$productType = $_product->getdata("type_id");
				if($parentId=="") {
					if($helper->isPreorder($productId) && !in_array($productType, $typeArray)) {
						$flag=1;
					}
				} else {
					if($helper->isPreorder($productId)) {
						$flag=1;
					}
				}
			}
			$storeId = $this->getStore()->getId();
			if (!Mage::helper('sales')->canSendNewOrderEmail($storeId)) {
				return $this;
			}
			// Get the destination email addresses to send copies to
			$copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
			$copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);
			// Start store emulation process
			$appEmulation = Mage::getSingleton('core/app_emulation');
			$initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);
			try {
				// Retrieve specified view block from appropriate design package (depends on emulated store)
				$paymentBlock = Mage::helper('payment')->getInfoBlock($this->getPayment())
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
			if ($this->getCustomerIsGuest()) {
				if($isPreorderComplete==1){
					$templateId = "preorder_complete_guest_email";
					$preorderFlag = 1;
				} else{
					if($flag==1){
						$templateId = "preorder_guest_email";
					} else {
						$templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
					}
				}
				$customerName = $this->getBillingAddress()->getName();
			} else {
				if($isPreorderComplete==1){
					$templateId = "preorder_complete_email";
					$preorderFlag = 1;
				} else{
					if($flag==1){
						$templateId = "preorder_email";
					} else {
						$templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
					}
				}
				$customerName = $this->getCustomerName();
			}
			$mailer = Mage::getModel('core/email_template_mailer');
			$emailInfo = Mage::getModel('core/email_info');
			$emailInfo->addTo($this->getCustomerEmail(), $customerName);
			if ($copyTo && $copyMethod == 'bcc') {
				// Add bcc to customer email
				foreach ($copyTo as $email) {
					$emailInfo->addBcc($email);
				}
			}
			$mailer->addEmailInfo($emailInfo);
			// Email copies are sent as separated emails if their copy method is 'copy'
			if ($copyTo && $copyMethod == 'copy') {
				foreach ($copyTo as $email) {
					$emailInfo = Mage::getModel('core/email_info');
					$emailInfo->addTo($email);
					$mailer->addEmailInfo($emailInfo);
				}
			}
			// Set all required params and send emails
			$mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
			$mailer->setStoreId($storeId);
			$mailer->setTemplateId($templateId);
			// $mailer->setTemplateId("webkul_test");
			if($preorderFlag!= 1) {
				$mailer->setTemplateParams(array(
					'order'			=> $this,
					'billing'		=> $this->getBillingAddress(),
					'payment_html'	=> $paymentBlockHtml
					)
				);
				$mailer->send();
				$this->setEmailSent(true);
				$this->_getResource()->saveAttribute($this, 'email_sent');
				return $this;
			}
		}
	}