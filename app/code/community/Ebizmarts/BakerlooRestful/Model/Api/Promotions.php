<?php

class Ebizmarts_BakerlooRestful_Model_Api_Promotions extends Ebizmarts_BakerlooRestful_Model_Api_Api {

    protected $_model   = "salesrule/rule";
    public $defaultSort = "code";

    public function get() {
        Mage::throwException('Not implemented.');
    }

    public function post() {
        Mage::throwException('Not implemented.');
    }

    /**
     * Validate provided coupon code.
     * Receives an order and validates coupon code.
     *
     * PUT
     */
    public function put() {

        if(!$this->getStoreId()) {
            Mage::throwException('Please provide a Store ID.');
        }

        Mage::app()->setCurrentStore($this->getStoreId());

        $data = $this->getJsonPayload();

        $quote = Mage::helper('bakerloo_restful/sales')->buildQuote($this->getStoreId(), $data, false);

        $posProductsCount = count($data->products);

        foreach($quote->getItemsCollection() as $item) {

            for($i=0; $i < $posProductsCount; $i++) {
                if($data->products[$i]->product_id == $item->getProductId()) {
                    $data->products[$i]->price = $item->getPrice();
                    $data->products[$i]->qty   = $item->getQty();
                }
            }

        }

        $quote->delete();

        return $data;

    }


}