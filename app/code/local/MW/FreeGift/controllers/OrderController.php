<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 11/21/13
 * Time: 3:03 PM
 * To change this template use File | Settings | File Templates.
 */
require_once Mage::getModuleDir('controllers', 'Mage_Sales') . DS . 'OrderController.php';

class MW_FreeGift_OrderController extends Mage_Sales_OrderController{
    public function reorderAction(){
        if (!$this->_loadValidOrder()) {
            return;
        }
        $order = Mage::registry('current_order');
        $cart = Mage::getSingleton('checkout/cart');
        $cartTruncated = false;
        /* @var $cart Mage_Checkout_Model_Cart */

        $items = $order->getItemsCollection();

        foreach ($items as $item) {
            try {
                $params = $item->getProductOptionByCode('info_buyRequest');
                $flag_add = true;
                /* If item is gift product */
                if ((isset($params['free_catalog_gift']) && $params['free_catalog_gift']) || (isset($params['freegift']) && $params['freegift']) || (isset($params['freegift_with_code']) && $params['freegift_with_code']))
                    continue;
                $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getStoreId())
                    ->load($item->getProductId());
                if($item->getParentItemId()){
                    continue;
                }
                if($product->getTypeId() == 'configurable'){
                    $stock_qty = 0;
                }else{
                    $stock_qty = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
                }

                $qty_4gift = 0;
                if($product->isSalable()){
                    if($item->getPrice() == 0 && $item->getParentItemId() == ""){
                        /** GIFT product */
                        /** quantity of product in stock less than qty product in order then set min qty for gift be: stock qty */
                        if((int)$item->getQtyOrdered() >= $stock_qty){
                            $qty_4gift = $stock_qty;
                            $flag_add = true;
                        }else{
                            $qty_4gift = (int)$item->getQtyOrdered();
                        }
                        /** Remove freegift */
                        $qty_4gift = 0;
                    }else{
                        $qty_4gift = (int)$item->getQtyOrdered();
                    }
                }
                if((int)$item->getPrice() == 0){
                    $item->removeItem($item->getId());
                    $item->save();
                    continue;
                }

                $item->setQtyOrdered((int)$item->getQtyOrdered());
                $cart->addOrderItem($item);
            } catch (Mage_Core_Exception $e){
                if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                    Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                }
                else {
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
        $this->_redirect('checkout/cart');
    }
}