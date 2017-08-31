<?php
class Kronosav_Repair_ConfirmController extends Mage_Core_Controller_Front_Action
{
    public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
    }
	
	public function yesAction() {
		$this->loadLayout();
		$repair_id = $this->getRequest()->getParam('repair_id');
		$collection = Mage::getModel('repair/repair')->load($repair_id);
		if($collection->getStatus()==0)
		{
			$customer = Mage::getModel('customer/customer')->load($collection->getEntityId());
			$firstname = $customer->getName();
			$email = $customer->getEmail();
			//Delivery Email
			//Getting the Store E-Mail Sender Name.
			 $senderName = Mage::getStoreConfig('trans_email/ident_general/name');

			 //Getting the Store General E-Mail.
			 $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
			 $emailTemplate = Mage::getModel('core/email_template')->loadDefault('accept_customer_email_template');
			 
			 $emailTemplateVariables = array();
			 $emailTemplateVariables['customer_name'] = $firstname;
			 $emailTemplateVariables['product_make'] = $collection->getProductMake();
			 $emailTemplateVariables['product_model'] = $collection->getProductModel();
			 $emailTemplateVariables['serial_no']=$collection->getSerialNo();
			 $emailTemplateVariables['repair_details'] = $collection->getRepairDetails();
			 $emailTemplateVariables['repair_cost'] = $collection->getRepairCost();
			 $emailTemplateVariables['repair_submission_date'] = $collection->getRepairSubmissionDate();
			 $subject = "Thanks for accepting";
			 $collection->setStatus("1")->save();
			 //Appending the Custom Variables to Template.
			 $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			 //to admin
			 $this->sendmail($firstname,$email,$processedTemplate,$subject,$senderEmail,$senderName); 
			 //to customer
			 $subject = "customer accepted";
			 $emailTemplate1 = Mage::getModel('core/email_template')->loadDefault('accept_admin_email_template');
			 $processedTemplate1 = $emailTemplate1->getProcessedTemplate($emailTemplateVariables);
			 $this->sendmail($senderName,$senderEmail,$processedTemplate1,$subject,$email,$firstname);
		 }
		 $this->renderLayout();
	}
	
	public function noAction() {
		$this->loadLayout();
		$repair_id = $this->getRequest()->getParam('repair_id');
		$collection = Mage::getModel('repair/repair')->load($repair_id);
		if($collection->getStatus()==0)
		{
			$customer = Mage::getModel('customer/customer')->load($collection->getEntityId());
			$firstname = $customer->getName();
			$email = $customer->getEmail();
			//Delivery Email
			//Getting the Store E-Mail Sender Name.
			 $senderName = Mage::getStoreConfig('trans_email/ident_general/name');

			 //Getting the Store General E-Mail.
			 $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
			 $emailTemplate = Mage::getModel('core/email_template')->loadDefault('reject_customer_email_template');
			 
			 $emailTemplateVariables = array();
			 $emailTemplateVariables['customer_name'] = $firstname;
			 $emailTemplateVariables['product_make'] = $collection->getProductMake();
			 $emailTemplateVariables['product_model'] = $collection->getProductModel();
			 $emailTemplateVariables['serial_no']=$collection->getSerialNo();
			 $emailTemplateVariables['repair_details'] = $collection->getRepairDetails();
			 $emailTemplateVariables['repair_cost'] = $collection->getRepairCost();
			 $emailTemplateVariables['repair_submission_date'] = $collection->getRepairSubmissionDate();
			 $subject = "Thanks for rejecting";
			 $collection->setStatus("4")->save();
			 //Appending the Custom Variables to Template.
			 $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			 //to admin
			 $this->sendmail($firstname,$email,$processedTemplate,$subject,$senderEmail,$senderName); 
			 //to customer
			 $subject = "customer rejected";
			 $emailTemplate1 = Mage::getModel('core/email_template')->loadDefault('reject_admin_email_template');
			 $processedTemplate1 = $emailTemplate1->getProcessedTemplate($emailTemplateVariables);
			 $this->sendmail($senderName,$senderEmail,$processedTemplate1,$subject,$email,$firstname); 
		 }
		 $this->renderLayout();
	}
		
		
	public function sendmail($firstname,$emailid,$processedTemplate,$subject,$senderEmail,$senderName)
	{
		//Sending E-Mail to Customers.
		 $mail = Mage::getModel('core/email')
		 ->setToName($firstname)
		 ->setToEmail($emailid)
		 ->setBody($processedTemplate)
		 ->setSubject($subject)
		 ->setFromEmail($senderEmail)
		 ->setFromName($senderName)
		 ->setType('html');

		try
		{
		//Confimation E-Mail Send
			$mail->send();
		}
		catch(Exception $error)
		{
			Mage::getSingleton('core/session')->addError($error->getMessage());
			return false;
		}
	}
}