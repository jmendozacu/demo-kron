<?php

    class Webkul_Preorder_PreorderController extends Mage_Core_Controller_Front_Action {

        public function completePreorderAction() {
            $customerId = Mage::getSingleton('customer/session')->getId();
            if($customerId>0) {
                $deleteItemArray=array();
                $helper = Mage::helper("preorder");
                $data = Mage::app()->getRequest()->getParams();
                $refNumber = $data["preorder_token"];
                $preorderId = $data["preorder_id"];
                $order_id = $data["order_id"];
                $time = $data["unique_number"];
                $orderId = 0;
                $productId=0;
                $childId=0;
                $childStockstatus = 1;
                if($refNumber!="") {
                    $collection = Mage::getModel("preorder/preorder")->getCollection()
                                                                     ->addFieldToFilter("ref_number",$refNumber)
                                                                     ->addFieldToFilter("customer_id",$customerId)
                                                                     ->addFieldToFilter("status",1)
                                                                     ->addFieldToFilter("preorder_id",$preorderId)
                                                                     ->addFieldToFilter("orderid",$order_id)
                                                                     ->addFieldToFilter("time",$time);

                    foreach ($collection as $item) {
                        if($item->getId()>0) {
                            $orderId = $item->getOrderid();
                            $productId = $item->getItemid();
                            $childId = $item->getChildid();
                            break;
                        }
                    }

                    if($productId>0 && $orderId>0) {
                        $cartHelper = Mage::helper('checkout/cart');
                        $items = $cartHelper->getCart()->getItems();
                        foreach ($items as $item) {
                            $itemId = $item->getItemId();
                            $deleteItemArray[] = $itemId;
                        }
                        if($childId!=0) {
                            $stockStatus = $helper->getStockStatus($childId);
                            if($stockStatus==0) {
                                $childStockstatus=0;
                            }
                        }
                        $stockStatus = $helper->getStockStatus($productId);
                        
                        if($stockStatus==1 && $childStockstatus==1) {
                           
                            $order = Mage::getModel("sales/order")->load($orderId);
                            $cart = Mage::getSingleton('checkout/cart');
                            $cartTruncated = false;
                            $items = $order->getItemsCollection();
                            foreach ($items as $item) {
                                try {
                                    $cart->addOrderItem($item);
                                } catch (Mage_Core_Exception $e) {
                                    if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                                        Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                                    } else {
                                        Mage::getSingleton('checkout/session')->addError($e->getMessage());
                                    }
                                    $this->_redirect('*/*/history');
                                } catch (Exception $e) {
                                    Mage::getSingleton('checkout/session')->addException($e,
                                        Mage::helper('checkout')->__('Cannot add the item to shopping cart.')
                                    );
                                    $this->_redirect('checkout/cart');
                                }
                            }
                            $cart->save();
                            $orderItemId = 0;
                            $parentItemId=0;
                            
                            $items = $cartHelper->getCart()->getItems();
                            foreach ($deleteItemArray as $deleteItem) {
                                $cartHelper->getCart()->removeItem($deleteItem)->save();
                            }
                            // $cartHelper = Mage::helper('checkout/cart');
                            // $items = $cartHelper->getCart()->getItems();
                            $quote = Mage::getSingleton('checkout/session')->getQuote();
                            $items = $quote->getAllVisibleItems();
                            foreach ($items as $item) {
                                $orderItemId = $item->getItemId();
                                $parentItemId = $item->getParentItemId();
                                break;
                            }
                            // $orderItemId=11111;
                            if($orderItemId>0) {
                                $ref = $refNumber."&".$orderItemId."&".$parentItemId;
                                $preorderRef = $preorderId."&".$orderId."&".$orderItemId;
                                Mage::getSingleton("core/session")->setPreorderReferenceNumber($ref);
                                Mage::getSingleton("core/session")->setPreorderOrderNumber($preorderRef);
                                Mage::getSingleton("core/session")->setPreorderTime($time);
                            }
                            $quote = Mage::getSingleton('checkout/session')->getQuote();
                            $quote->save();
                            $this->_redirect('checkout/cart');
                        } else{
                            $this->_redirect('customer/account');
                        }
                    } else {
                        $this->_redirect('customer/account');
                    }
                } else{
                   $this->_redirect('customer/account');
                }
            } else {
                $this->_redirect('customer/account');
            }
        }
    }