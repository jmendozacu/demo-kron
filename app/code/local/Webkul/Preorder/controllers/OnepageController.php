<?php
	# Controllers are not autoloaded so we will have to do it manually:
	require_once 'Mage/Checkout/controllers/OnepageController.php';
	class Webkul_Preorder_OnepageController extends Mage_Checkout_OnepageController {

		public function saveBillingAction()
		{
			$helper = Mage::helper("preorder");
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
			$customerId = Mage::getSingleton('customer/session')->getId();
			if ($this->_expireAjax()) {
			  return;
			}
			if ($this->getRequest()->isPost()) {
				$data = $this->getRequest()->getPost('billing', array());
				$customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
				
				if (isset($data['email'])) {
					$data['email'] = trim($data['email']);
				}
				$result = $this->getOnepage()->saveBilling($data, $customerAddressId);

				if (!isset($result['error'])) {
					if($helper->isPreorderCompleteOrder()) {
						$result['goto_section'] = 'payment';
						$result['update_section'] = array(
							'name' => 'payment-method',
							'html' => $this->_getPaymentMethodsHtml()
						);
					} else {
						if ($this->getOnepage()->getQuote()->isVirtual()) {
							$result['goto_section'] = 'payment';
							$result['update_section'] = array(
								'name' => 'payment-method',
								'html' => $this->_getPaymentMethodsHtml()
							);
						} elseif (isset($data['use_for_shipping']) && $data['use_for_shipping'] == 1) {
							$result['goto_section'] = 'shipping_method';
							$result['update_section'] = array(
								'name' => 'shipping-method',
								'html' => $this->_getShippingMethodsHtml()
							);
							$result['allow_sections'] = array('shipping');
							$result['duplicateBillingInfo'] = 'true';
						} else {
							$result['goto_section'] = 'shipping';
						}
					}
				}
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
			}
		}
	}