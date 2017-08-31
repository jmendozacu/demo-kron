<?php

class Ebizmarts_BakerlooRestful_Model_V1_Paymentmethods extends Ebizmarts_BakerlooRestful_Model_V1_Api {

    /**
     * Process GET requests.
     *
     * @return type
     * @throws Exception
     */
    public function get() {

        $store = $this->getStoreId();

        return Mage::helper('bakerloo_payment')->getBakerlooPaymentMethods($store);

    }

}