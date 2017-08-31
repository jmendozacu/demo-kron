<?php

class Ebizmarts_BakerlooRestful_Model_V1_Invoices extends Ebizmarts_BakerlooRestful_Model_V1_Api {

    protected $_model = "sales/order_invoice";

    public function _createDataObject($id = null, $data = null) {
        $result = null;

        if(is_null($data)) {
            $invoice = Mage::getModel($this->_model)->load($id);
        }
        else {
            $invoice = $data;
        }

        if($invoice->getId()) {

            $invoiceItems = array();

            foreach($invoice->getItemsCollection() as $item) {

                $invoiceItems[]= array(
                    'product_id' => (int)$item->getProductId(),
                    'qty'        => (int)($item->getQty() * 1),
                    'price'      => (float)$item->getPrice(),
                    'name'       => $item->getName(),
                    'sku'        => $item->getSku(),
                );

            }

            $result = array(
                            "entity_id"            => (int)$invoice->getId(),
                            "increment_id"         => (int)$invoice->getIncrementId(),
                            "state"                => $invoice->getStateName(),
                            "created_at"           => $invoice->getCreatedAt(),
                            "updated_at"           => $invoice->getUpdatedAt(),
                            "store_id"             => (int)$invoice->getStoreId(),
                            "base_grand_total"     => (float)$invoice->getBaseGrandTotal(),
                            "base_total_paid"      => (float)$invoice->getBaseTotalPaid(),
                            "grand_total"          => (float)$invoice->getGrandTotal(),
                            "total_paid"           => (float)$invoice->getTotalPaid(),
                            "tax_amount"           => (float)$invoice->getTaxAmount(),
                            'products'             => $invoiceItems,
        	);
        }

        return $result;
    }

}