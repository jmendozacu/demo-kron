<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Administrator
 * Date: 11/21/13
 * Time: 1:54 PM
 * To change this template use File | Settings | File Templates.
 */

class MW_FreeGift_Model_Order extends Mage_Sales_Model_Order{
    protected function _canReorder($ignoreSalable = false)
    {
        if ($this->canUnhold() || $this->isPaymentReview() || !$this->getCustomerId()) {
            return false;
        }
        $order = $this;

        $products = array();
        foreach ($this->getItemsCollection() as $item) {
            $products[$item->getProductId()]['id'] = $item->getProductId();
            $products[$item->getProductId()]['price'] = (int)$item->getPrice();
        }

        if (!empty($products)) {
            /*
             * @TODO ACPAOC: Use product collection here, but ensure that product
             * is loaded with order store id, otherwise there'll be problems with isSalable()
             * for configurables, bundles and other composites
             *
             */
            /*
            $productsCollection = Mage::getModel('catalog/product')->getCollection()
                ->setStoreId($this->getStoreId())
                ->addIdFilter($products)
                ->addAttributeToSelect('status')
                ->load();

            foreach ($productsCollection as $product) {
                if (!$product->isSalable()) {
                    return false;
                }
            }
            */
            $items = $this->getAllItems();
            foreach ($products as $_product) {
                $product = Mage::getModel('catalog/product')
                    ->setStoreId($this->getStoreId())
                    ->load($_product['id']);
                if (!$product->getId() || (!$ignoreSalable && !$product->isSalable())) {
                    if($_product['price'] == 0){
                        /** is Gift product */
                    }else{
                        return false;
                    }
                }
            }
        }
        if ($this->getActionFlag(self::ACTION_FLAG_REORDER) === false) {
            return false;
        }

        return true;
    }
}