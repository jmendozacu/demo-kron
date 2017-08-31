<?php

/*
 * Web POS by Magestore.com
 * Version 2.3
 * Updated by Daniel - 12/2015
 */

class Magestore_Webpos_Block_Payment_Method_Multipayment_Multipaymentforpos extends Mage_Payment_Block_Form {

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('webpos/webpos/payment/method/form/multipaymentforpos.phtml');
    }
    
    public function getActiveMethods(){
        $methods = Mage::getModel('webpos/source_adminhtml_multipaymentforpos')->getAllowPaymentMethodsWithLabel();
		$storeId = Mage::app()->getStore()->getStoreId();
		$paymentsForSplit = Mage::getStoreConfig('payment/multipaymentforpos/payments',$storeId);
		if(count(explode(',',$paymentsForSplit)) > 0){
			foreach($methods as $methodCode => $methodTitle){
				if(!in_array($methodCode,explode(',',$paymentsForSplit))){
					unset($methods[$methodCode]);
				}
			}
		}
        return $methods;
    }
}
