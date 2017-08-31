<?php

class Ebizmarts_BakerlooPayment_Model_Observer {

    public function paymentMethodFieldset($observer) {
        $block = $observer->getEvent()->getBlock();

        //Add bakerloo payment information fieldset to customer groups
        if($block instanceof Mage_Adminhtml_Block_Customer_Group_Edit_Form) {

            $fieldset = $block->getForm()->addFieldset('bakerloo_payment_methods_fieldset', array('legend'=>Mage::helper('customer')->__('POS')));

            $fieldset->addField('bakerloo_payment_methods', 'multiselect',
                array(
                    'name'     => 'bakerloo_payment_methods',
                    'label'    => Mage::helper('bakerloo_payment')->__('POS Payment Methods'),
                    'title'    => Mage::helper('bakerloo_payment')->__('POS Payment Methods'),
                    'note'     => Mage::helper('bakerloo_payment')->__('All payment methods are enabled by default or if none is selected.'),
                    'required' => false,
                    'values'   => Mage::getSingleton('bakerloo_payment/source_paymentmethods')->getAllOptions(),
                    'value'    => Mage::registry('current_group')->getBakerlooPaymentMethods(),
                )
            );

        }

    }

    public function saveBakerlooData($observer) {
        $group = $observer->getEvent()->getObject();

        $postPayment = Mage::app()->getRequest()->getParam('bakerloo_payment_methods');

        if(!is_null($postPayment)) {
            $group->setBakerlooPaymentMethods(implode(',', $postPayment));
        }

    }

    /**
     * Check for invoice status on PayPal's API.
     *
     * @param $observer
     */
    public function payPalInvoiceDetails($observer) {
        $order = Mage::registry('current_order');

        if(!is_null($order)) {
            if('bakerloo_paypalhere' == $order->getPayment()->getMethod()) {
                $payment = $order->getPayment()->getMethodInstance();
                $info    = $payment->getInfoInstance();

                $transactionId = Mage::helper('bakerloo_payment')->getPayPalTxId($info->getPoNumber());

                if($transactionId != 'Cash') {
                    $transactionDetails = $payment->GetTransactionDetails($transactionId);

                    $_transactionDetails = Mage::helper('bakerloo_payment')->processNvpData($transactionDetails);

                    if(is_array($_transactionDetails) && !empty($_transactionDetails)) {
                        if( isset($_transactionDetails['ACK']) && ($_transactionDetails['ACK'] != 'Failure') ) {
                            Mage::register('bakerloo_paypalhere_transaction_details', $_transactionDetails);
                        }
                    }
                }

            }
        }
    }
}