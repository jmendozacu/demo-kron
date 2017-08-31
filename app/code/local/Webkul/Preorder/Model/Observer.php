<?php

	class Webkul_Preorder_Model_Observer {

		public function productSaveAfter(Varien_Event_Observer $observer) {
			$emailArray = array();
			$notifyArray = array();
			$typeArray = array('bundle','grouped','configurable');
			$helper = Mage::helper("preorder");
			$product = $observer->getEvent()->getProduct();
			$product = Mage::getModel('catalog/product')->load($product->getId());
			$qty = $product->getStockItem()->getQty();
			$productType = $product->getdata("type_id");
			$attr = $product->getResource()->getAttribute("wk_preorder");
			$disable_val_id = $attr->getSource()->getOptionId("Disable");
			$stockStatus = $helper->getStockStatus($product->getId());
			if($stockStatus==1) {
				$collection = Mage::getModel("preorder/preorder")->getCollection()
																->addFieldToFilter("status",1)
																->addFieldToFilter("notify",0);
				foreach ($collection as $item) {
					$itemId = $item->getItemid();
					$childId = $item->getChildid();
					if(in_array($productType, $typeArray)) {
						if($childId>0) {
							if($childId==$product->getId()) {
								$emailArray[] = $item->getCustomerId();
								$notifyArray[] = $item->getPreorderId();
							}
						}
					} else {
						if($childId==0) {
							if($itemId==$product->getId()) {
								$emailArray[] = $item->getCustomerId();
								$notifyArray[] = $item->getPreorderId();
							}
						}
					}
				}
				if($helper->isAutoEmail()){
					$helper->sendEmail($emailArray, $product);
					$helper->notifyStatus($notifyArray);
				}
			} else {
				if($helper->isPreorder($product->getId()) && in_array($productType, $typeArray)) {
					$product->setWkPreorder($disable_val_id);
					$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
					$stockItem->setData('is_in_stock',1);
					$stockItem->save();
					$product->getResource()->save($product);
					Mage::getSingleton("core/session")->addError("Sorry you can't set whole Bundle or Grouped or Configurable Product as preorder");
				}
			}
		}

		public function updatePrice(Varien_Event_Observer $observer) {
			$flag=0;
			$quoteItem = $observer->getQuoteItem();
			$_item = $quoteItem->getProduct();
			$product = Mage::getModel("catalog/product")->load($quoteItem->getProduct()->getEntityId());
			$productType= $product->getTypeId();
			$params = Mage::app()->getRequest()->getParams();
			$helper = Mage::helper("preorder");
			$preorderType = $helper->getPreorderType();
			$stockStatus = $helper->getStockStatus($product->getId());
			$percentAccept = $helper->getPreorderPercent($product->getId());

			$regularPrice = $_item->getFinalPrice();
			$specialPrice = $_item->getSpecialPrice();
			if($helper->isInOffer($product)) {
				$finalPrice = $specialPrice;
			} else {
				$finalPrice = $regularPrice;
			}
			if($preorderType==1) {
				if($productType=="configurable") {
					
					if(isset($params['super_attribute'])) {
						if(count($params['super_attribute'])>0) {
							$pro = $product->getTypeInstance(true)->getProductByAttributes($params['super_attribute'], $product);
							if($helper->isPreorder($pro->getId())) {
								$flag=1;
							}
						}
					}
					
				} elseif($productType=="bundle") {

				} else {
					if($stockStatus!=1) {
						if($helper->isPreorder($product->getId())) {
							$flag=1;
						}
					}
				}
			}
			if($flag==1) {
				if($percentAccept >= 0) {
					$newPrice = ($finalPrice*$percentAccept)/100;
					$quoteItem->setCustomPrice($newPrice);
					$quoteItem->setOriginalCustomPrice($newPrice);
					$quoteItem->getProduct()->setIsSuperMode(true);
				}
			}
			
		}

		public function afterPlaceOrder(Varien_Event_Observer $observer) {
			$helper = Mage::helper("preorder");
			$orderIdArray = $observer->getOrderIds();
			$completeStatusId=0;
			$orderId = $orderIdArray[0];
			$order = Mage::getModel("sales/order")->load($orderId);
			$customerId = $order->getCustomerId();
			$incrementId = $order->getIncrementId();
			$preorderCompleteRef = Mage::getSingleton("core/session")->getPreorderCompleteStatus();
			$temp = explode("=", $preorderCompleteRef);
			if(isset($temp[1])) {
				$completeStatusId = trim($temp[1]);
			}
			if($incrementId==$completeStatusId) {
				$ids = Mage::getSingleton("core/session")->getPreorderOrderNumber();
				$idArray = explode("&", $ids);
				$preorderOrderId = $idArray[1];
				$preorderTime = Mage::getSingleton("core/session")->getPreorderTime();
				$preorderModel = Mage::getModel("preorder/preorder")->load($idArray[0]);
				$remainingAmount = $preorderModel->getRemainingAmount();
				$qty = $preorderModel->getQty();
				//Update sales_flat_order table
				$orderModel = Mage::getModel("preorder/order")->load($preorderOrderId);
				$baseGrandTotal = $orderModel->getBaseGrandTotal()+$remainingAmount;
				$baseSubtotal = $orderModel->getBaseSubtotal()+$remainingAmount;
				$grandTotal = $orderModel->getGrandTotal()+$remainingAmount;
				$subtotal = $orderModel->getSubtotal()+$remainingAmount;
				$baseSubtotalInclTax = $orderModel->getBaseSubtotalInclTax()+$remainingAmount;
				$subtotalInclTax = $orderModel->getSubtotalInclTax()+$remainingAmount;
				$orderData = array("base_grand_total"=>$baseGrandTotal,"base_subtotal"=>$baseSubtotal,"grand_total"=>$grandTotal,"subtotal"=>$subtotal,"base_subtotal_incl_tax"=>$baseSubtotalInclTax,"subtotal_incl_tax"=>$subtotalInclTax);
				$orderModel->addData($orderData);
				try {
					$orderModel->setId($preorderOrderId)->save();
				} catch (Exception $e) {
					$e->getMessage();
				}
				// Update sales_flat_order table
				// Update sales_flat_order_grid table
				$orderGridModel = Mage::getModel("preorder/grid")->load($preorderOrderId);
				$baseGrandTotal = $orderGridModel->getBaseGrandTotal()+$remainingAmount;
				$grandTotal = $orderGridModel->getGrandTotal()+$remainingAmount;
				$orderData = array("base_grand_total"=>$baseGrandTotal,"grand_total"=>$grandTotal);
				$orderGridModel->addData($orderData);
				try {
					$orderGridModel->setId($preorderOrderId)->save();
				} catch (Exception $e) {
					$e->getMessage(); 
				}
				// Update sales_flat_order_grid table
				// Update sales_flat_order_payment table
				$orderPaymentModel = Mage::getModel("preorder/payment")->load($preorderOrderId);
				$baseAmountOrdered = $orderPaymentModel->getBaseAmountOrdered()+$remainingAmount;
				$amountOrdered = $orderPaymentModel->getAmountOrdered()+$remainingAmount;
				$orderData = array("base_amount_ordered"=>$baseAmountOrdered,"amount_ordered"=>$amountOrdered);
				$orderPaymentModel->addData($orderData);
				try {
					$orderPaymentModel->setId($preorderOrderId)->save();
				} catch (Exception $e) {
					$e->getMessage(); 
				}
				// Update sales_flat_order_payment table
				// Update sales_flat_order_item table
				$orderItemCollection = Mage::getModel("preorder/item")->getCollection()
																->addFieldToFilter("order_id",$preorderOrderId);
				foreach ($orderItemCollection as $itemValue) {
					$id = $itemValue->getId();
					$orderItemModel = Mage::getModel("preorder/item")->load($id);
					$price = $orderItemModel->getPrice()+$remainingAmount/$qty;
					$basePrice = $orderItemModel->getBasePrice()+$remainingAmount/$qty;
					$rowTotal = $orderItemModel->getRowTotal()+$remainingAmount;
					$baseRowTotal = $orderItemModel->getBaseRowTotal()+$remainingAmount;
					$priceInclTax = $orderItemModel->getPriceInclTax()+$remainingAmount;
					$basePriceInclTax = $orderItemModel->getBasePriceInclTax()+$remainingAmount;
					$rowTotalInclTax = $orderItemModel->getRowTotalInclTax()+$remainingAmount;
					$baseRowTotalInclTax = $orderItemModel->getBaseRowTotalInclTax()+$remainingAmount;
					$orderData = array("price"=>$price,"base_price"=>$basePrice,"row_total"=>$rowTotal,"base_row_total"=>$baseRowTotal,"price_incl_tax"=>$priceInclTax,"base_price_incl_tax"=>$basePriceInclTax,"row_total_incl_tax"=>$rowTotalInclTax,"base_row_total_incl_tax"=>$baseRowTotalInclTax);
					$orderItemModel->addData($orderData);
					try {
						$orderItemModel->setId($id)->save();
					} catch (Exception $e) {
						$e->getMessage(); 
					}
				}
				// Update sales_flat_order_item table --!>
				//<!--send PreOrder Complete Mail
				$helper->sendCompletePreOrderEmail($order);
				//send PreOrder Complete Mail --!>
				//<!-- delete item
				try {
					Mage::getModel("preorder/order")->setId($orderId)->delete();
				} catch (Exception $e){
					$e->getMessage(); 
				}
				try {
					Mage::getModel("preorder/grid")->setId($orderId)->delete();
				} catch (Exception $e){
					$e->getMessage(); 
				}
				try {
					Mage::getModel("preorder/payment")->setId($orderId)->delete();
				} catch (Exception $e){
					$e->getMessage(); 
				}
				$orderItemCollection = Mage::getModel("preorder/item")->getCollection()
																->addFieldToFilter("order_id",$orderId);
				foreach ($orderItemCollection as $itemValue) {
					$id = $itemValue->getId();
					try {
						Mage::getModel("preorder/item")->setId($id)->delete();
					} catch (Exception $e){
						$e->getMessage(); 
					}
				}
				// delete item --!>
				//Update Preorder Status
				$data = array("status"=>2);
				$preorderModel->addData($data);
				try {
					$preorderModel->setId($idArray[0])->save();
				} catch (Exception $e) {
					$e->getMessage();
				}
			} else {
				$preorderType = $helper->getPreorderType();
				$time = strtotime(date('y-m-d h:i:s'));
				$typeArray = array('bundle','configurable');
				$helper = Mage::helper("preorder");
				$model = Mage::getModel("preorder/preorder");
				$orderIdArray = $observer->getOrderIds();
				$orderId = $orderIdArray[0];
				$order = Mage::getModel("sales/order")->load($orderId);
				
				$qty = $order->getTotalQtyOrdered();
				$grandTotal = $order->getGrandTotal();
				$subTotal = $order->getSubtotal();
				$baseSubTotal = $order->getBaseSubtotal();
				$customerId = $order->getCustomerId();
				$refNumber = $helper->getCode();
				$price = 0;
				$orderedItems = $order->getAllItems();
				foreach ($orderedItems as $item) {
					$orderedQty = $item->getQtyOrdered();
					$proId = $item->getProductId();
					$preorderPercent = $helper->getPreorderPercent($proId);
					$price+=$orderedQty*$helper->getPrice($proId);
				}
				$remainingAmount = ((100-$preorderPercent)*$subTotal)/$preorderPercent;
				// $remainingAmount = $price-$subTotal;
				foreach ($order->getAllItems() as $item) {
					$parentId = $item->getParentItemId();
					$productId = $item->getProductId();
					$_product = Mage::getModel('catalog/product')->load($productId);
					$productType = $_product->getdata("type_id");
					if($preorderType==1) {
						$data = array("orderid"=>$orderId,"qty"=>$qty,"paid_amount"=>$grandTotal,"remaining_amount"=>$remainingAmount,"ref_number"=>$refNumber,"status"=>1,"type"=>$preorderType,"preorder_percent"=>$preorderPercent,"customer_id"=>$customerId,"time"=>$time);
					} else {
						$data = array("orderid"=>$orderId,"qty"=>$qty,"paid_amount"=>$grandTotal,"remaining_amount"=>0,"ref_number"=>$refNumber,"status"=>1,"type"=>$preorderType,"preorder_percent"=>0,"customer_id"=>$customerId,"time"=>$time);
					}
					if($parentId=="") {
						if($helper->isPreorder($productId) && !in_array($productType, $typeArray)) {
							$model->setOrderid($orderId)
								->setData($data)
								->setRand(1)
								->setItemid($productId)
								->save();
						}
					} else {
						if($helper->isPreorder($productId)) {
							$parId = Mage::getModel('sales/order_item')->load($parentId)->getProductId();
							$model->setOrderid($orderId)
									->setData($data)
									->setItemid($parId)
									->setChildid($productId)
									->setRand(1)
									->save();
						}
					}
				}
			}
		}

		public function beforeAddToCart(Varien_Event_Observer $observer) {
			$isBundle = 0;
			$helper = Mage::helper("preorder");
			$preorderType = $helper->getPreorderType();
			if($preorderType==1) {
				$flag=0;
				$cart = Mage::getSingleton("checkout/cart")->getQuote();
				if(Mage::helper("checkout/cart")->getItemsCount()!=0) {
					foreach($cart->getAllItems() as $item) {
						$currentProductId = $item->getProductId();
						$product = Mage::getModel("catalog/product")->load($currentProductId);
						$productType = strtolower($product->getTypeId());
						if($productType=="bundle") {
							$isBundle=1;
						}

						if($helper->isPreorder($currentProductId)) {
							$flag=1;
						}
					}
					if($flag==1) {
						$observer->getControllerAction()->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
						Mage::getSingleton('checkout/session')->addError(Mage::helper('preorder')->__('You can not add other Product with Preorder Product.'));
						Mage::app()->getResponse()->setRedirect(Mage::getUrl("checkout/cart"));
					}
				}
			}
		}

		public function deleteItemFromCart(Varien_Event_Observer $observer) {
			$flag = 0;
			$str = Mage::getSingleton("core/session")->getPreorderReferenceNumber();
			$ref = explode("&", $str);
			$refNumber = $ref[0];
			$preOrderItemId = $ref[1];
			$cartHelper = Mage::helper('checkout/cart');
			$items = $cartHelper->getCart()->getItems();
			foreach ($items as $item) {
				$orderItemId = $item->getItemId();
				if($orderItemId==$preOrderItemId) {
					$flag=1;
				}
			}
			if($flag==0) {
				Mage::getSingleton("core/session")->setPreorderReferenceNumber("");
			}
		}

		public function afterAddToCart(Varien_Event_Observer $observer) {
			$flag=0;
			$arr = array();
			$helper = Mage::helper("preorder");
			$preorderType = $helper->getPreorderType();
			if($preorderType==1) {
				$cartHelper = Mage::helper('checkout/cart');
				$items = $cartHelper->getCart()->getItems();
				foreach ($items as $item) {
					$itemId = $item->getItemId();
					$parentId = $item->getParentItemId();
					$currentProductId = $item->getProductId();
					if($helper->isPreOrder($currentProductId)) {
						$flag=1;
						$arr['item_id'] = $itemId;
						if($parentId!=""){
							$arr['parent_item_id'] = $parentId;
						}
						break;
					}
				}
				if($flag==1) {
					$cartHelper = Mage::helper('checkout/cart');
					$cartItemsQty = $cartHelper->getItemsCount();
					$items = $cartHelper->getCart()->getItems();
					foreach ($items as $item) {
						$itemId = $item->getItemId();
						$parentId = $item->getParentItemId();
						if(in_array($itemId, $arr)) {

						} else {
							$cartHelper->getCart()->removeItem($itemId)->save();
						}
					}
					if($cartItemsQty>1){
						Mage::getSingleton('checkout/session')->addError(Mage::helper('preorder')->__("You can not add other Product with Preorder Product. You can add only one preorder at a time."));
					}
				}
			}
			if($helper->isPreorderCompleteOrder()) {
				// $helper->emptyOtherItems();
			}
		}

		public function updateCart(Varien_Event_Observer $observer) {
			$helper = Mage::helper("preorder");
			$preorderType = $helper->getPreorderType();
			$customerId = Mage::getSingleton('customer/session')->getId();
			$str = Mage::getSingleton("core/session")->getPreorderReferenceNumber();
			if($str!="") {
				$time = Mage::getSingleton("core/session")->getPreorderTime();
				$ref = explode("&", $str);
				$refNumber = $ref[0];
				$preOrderItemId = $ref[1];
				$cartHelper = Mage::helper('checkout/cart');
				$items = $cartHelper->getCart()->getItems();
				foreach ($items as $item) {
					$qty=0;
					$item_id = $item->getItemId();
					$itemQty = $item->getQty();
					if($preOrderItemId==$item_id) {
						$collection = Mage::getModel("preorder/preorder")->getCollection()
																		->addFieldToFilter("ref_number",$refNumber)
																		->addFieldToFilter("customer_id",$customerId)
																		->addFieldToFilter("status",1)
																		->addFieldToFilter("itemid",$item->getProductId())
																		->addFieldToFilter("time",$time);
						foreach ($collection as $_item) {
							$qty = $_item->getQty();
						}
						if($qty!=$itemQty) {
							Mage::getSingleton('checkout/session')->addError(Mage::helper('preorder')->__("You Can't Update Quantity"));
							$item->setQty($qty);
							// Mage::app()->getResponse()->setRedirect(Mage::getUrl("checkout/cart"));
						}
					}
				}
			}
		}

		public function afterSaveCart(Varien_Event_Observer $observer) {
			$flag = 0;
			$remainingAmount="";
			$helper = Mage::helper("preorder");
			$preorderType = $helper->getPreorderType();
			$percentAccept = $helper->getPreorderPercent();
			$customerId = Mage::getSingleton('customer/session')->getId();
			$str = Mage::getSingleton("core/session")->getPreorderReferenceNumber();
			$time = Mage::getSingleton("core/session")->getPreorderTime();
			$ref = explode("&", $str);
			$refNumber = 0;
			$preOrderItemId = 0;

			if(isset($ref[0])){
				$refNumber = $ref[0];
			}
			if(isset($ref[1])){
				$preOrderItemId = $ref[1];
			}
			$quote = Mage::getSingleton('checkout/session')->getQuote();
			$quoteId = $quote->getId();
			foreach($quote->getAllVisibleItems() as $quote_item) {
				$item_id = $quote_item->getItemId();
				$item_parent_id = $quote_item->getItemId();
				$productId = $quote_item->getProductId();
				$product = Mage::getModel("catalog/product")->load($productId);
				$productType = strtolower($product->getTypeId());
				if($productType=="bundle") {
					$filter = "";
				} else {
					$filter = "itemid";
				}
				$model = Mage::getModel("preorder/quoteitem");
				if($preOrderItemId==$item_id ) {
					$qty=0;
					$collection = Mage::getModel("preorder/preorder")->getCollection()
																	->addFieldToFilter("ref_number",$refNumber)
																	->addFieldToFilter("customer_id",$customerId)
																	->addFieldToFilter("status",1)
																	->addFieldToFilter("itemid",$productId)
																	->addFieldToFilter("time",$time);
					foreach ($collection as $item) {
						if($item->getId()>0) {

							$remainingAmount = $item->getRemainingAmount();
							$qty = $item->getQty();
							$quote_item->setCustomPrice($remainingAmount/$qty);
							$quote_item->setOriginalCustomPrice($remainingAmount/$qty);
							$quote_item->setOriginalCustomPrice($remainingAmount/$qty);
							$quote_item->setRowTotal($remainingAmount);
							$quote_item->setSubtotal($remainingAmount);
							$quote_item->setBaseSubtotal($remainingAmount);
							$quote_item->getProduct()->setIsSuperMode(true);
							$flag=1;
						}
						break;
					}
					if($qty>0) {
						$quote_item->setQty($qty);
					}
				}
				break;
			}
			$quote->save();
			$quote->setTotalsCollectedFlag(false)->collectTotals();


			if($preorderType==1) {
				$params = Mage::app()->getRequest()->getParams();
				$quote = Mage::getSingleton('checkout/session')->getQuote();
				$flag=0;
				$status=1;

				foreach($quote->getAllItems() as $quote_item) {
					
					$item_id = $quote_item->getItemId();
					
					$item_parent_id = $quote_item->getItemId();
					$productId = $quote_item->getProductId();
					$product = Mage::getModel("catalog/product")->load($productId);
					$productType = strtolower($product->getTypeId());
					
					if($productType=="configurable") {
						$stockStatus = $helper->getStockStatus($productId);
						if($stockStatus!=1) {
							$status=0;
						}
					}
					if($helper->isPreorder($productId)) {
						$flag=1;
					}

					if($productType!="configurable") {
						break;
					}

				}

				if($flag==1 && $status==1) {
					
					$quote = Mage::getSingleton('checkout/session')->getQuote();
					foreach($quote->getAllVisibleItems() as $quote_item) {
						$itemId = $quote_item->getItemId();
						
						$productId = $quote_item->getProductId();
						$product = Mage::getModel("catalog/product")->load($productId);
						$regularPrice = $product->getFinalPrice();
						$specialPrice = $product->getSpecialPrice();
						if($helper->isInOffer($product)) {
							$finalPrice = $specialPrice;
						} else {
							$finalPrice = $regularPrice;
						}
						if($percentAccept >= 0) {
							$customPrice = $quote_item->getCustomPrice();

							if($customPrice=="") {
								$finalPrice = $quote_item->getRowTotal();
								

								$newPrice = ($finalPrice*$percentAccept)/100;
								$quote_item->setCustomPrice($newPrice);
								$quote_item->setOriginalCustomPrice($newPrice);

								$quote_item->setRowTotal($newPrice);
								$quote_item->setSubtotal($newPrice);
								$quote_item->setBaseSubtotal($newPrice);
								$quote_item->getProduct()->setIsSuperMode(true);
							}

						}
						if($itemId==$preOrderItemId) {

							$quote->removeItem($itemId);
							$quote->collectTotals()->save();
							Mage::getSingleton('checkout/session')->addError(Mage::helper('preorder')->__("There some error while processing your request. Please add product in cart again."));
						}
						break;
					}
				}
			}

		}

		public function customerLogin(Varien_Event_Observer $observer) {
			$flag=0;
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			$helper = Mage::helper("preorder");
			$preorderType = $helper->getPreorderType();
			if($preorderType==1) {
				if ($customer->getId()) {
					$storeIds = Mage::app()->getWebsite(Mage::app()->getWebsite()->getId())->getStoreIds();
					$quote = Mage::getModel('sales/quote')->setSharedStoreIds($storeIds)->loadByCustomer($customer);
					if ($quote) {
						$collection = $quote->getItemsCollection(false);
						if ($collection->count() > 0) {
							foreach( $collection as $item ) {
								if($item && $item->getId()) {
									$quote->removeItem($item->getId());
									$quote->collectTotals()->save();
								}
							}
						}
					}
				}
			}
		}
	}