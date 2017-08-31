<?php

class Kronosav_Loan_Block_Dashboardpage extends Mage_Customer_Block_Account_Dashboard  
{
 public function appdetails()
 {
   $customer = Mage::getSingleton('customer/session')->getCustomer();
    /* Get the customer's full name */
   $entityid = $customer->getEntityId();
  
   $loan = Mage::getModel('loan/loan')
                        ->getCollection()
						->addFieldToSelect('*')
                        ->addFieldToFilter('entity_id',$entityid)
						->setOrder('loan_id','DESC')
						->getData();
   
    return $loan;
 }
}


