<?php

class Kronosav_Appointment_Block_Dashboardpage extends Mage_Customer_Block_Account_Dashboard  
{
 public function appdetails()
 {
   $customer = Mage::getSingleton('customer/session')->getCustomer();
    /* Get the customer's full name */
   $entityid = $customer->getEntityId();
  
   $appointment = Mage::getModel('appointment/appointment')
                        ->getCollection()
						->addFieldToSelect('*')
                        ->addFieldToFilter('entity_id',$entityid)
						->setOrder('appointment_id','DESC')
						->getData();
   
    return $appointment;
 }
}


