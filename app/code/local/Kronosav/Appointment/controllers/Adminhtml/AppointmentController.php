<?php 
class Kronosav_Appointment_Adminhtml_AppointmentController extends Mage_Adminhtml_Controller_Action
{ 
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('appointment/manage_appointment')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		return $this;
	}
	
	public function indexAction() 
	{
		$this->_initAction();       
		$this->_addContent($this->getLayout()->createBlock('appointment/adminhtml_appointment'));
		$this->renderLayout();
	} 
	
	public function editAction()
	{
		$appointmentId     = $this->getRequest()->getParam('id');
		$appointmentModel  = Mage::getModel('appointment/appointment')->load($appointmentId);
		$entityid=$appointmentModel->getEntityId();
		$customerModel = Mage::getModel('customer/customer')->load($entityid);
		
		if ($appointmentModel->getId() || $appointmentId == 0) {
 
			Mage::register('appointmentedit_data', $appointmentModel);
		
			Mage::register('customeredit_data', $customerModel);
 
			$this->loadLayout();
			$this->_setActiveMenu('appointment/manage_appointment');
		   
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
		   
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		   
			$this->_addContent($this->getLayout()->createBlock('appointment/adminhtml_appointment_edit'))
				 ->_addLeft($this->getLayout()->createBlock('appointment/adminhtml_appointment_edit_tabs'));
			   
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('appointment')->__('Appointment does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function createAction()
	{
		$appointmentId     = $this->getRequest()->getParam('id');
				
		$customerModel  = Mage::getModel('customer/customer')->load($appointmentId);
       
		if ($customerModel->getEntityId()|| $appointmentId == 0) {

			Mage::register('customeredit_data', $customerModel);
			
			$this->loadLayout();
			$this->_setActiveMenu('appointment/manage_appointment');
		   
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
		   
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		   
			$this->_addContent($this->getLayout()->createBlock('appointment/adminhtml_customer_edit'))
				 ->_addLeft($this->getLayout()->createBlock('appointment/adminhtml_customer_edit_tabs'));
			   
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('appointment')->__('Appointment does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	/*pdf Start*/
	public function createPdfAction()
	{
		$line = 120;
		// create instance of Zend_Pdf()
		$pdf = new Zend_Pdf();
		// set new page, page size and font
		$page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
		
		$page->setFont($font, 12);
		
		$appointmentIds = $this->getRequest()->getParam('id');
		
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				
				$i = 0;
				foreach($appointmentIds as $appointmentId) { 
					$appointmentModel = Mage::getModel('appointment/appointment')->load($appointmentId);	
					$appointment = $appointmentModel->getData();
					
					$getCustomerId = $appointment['entity_id'];
					$customer = Mage::getModel('customer/customer')->load($getCustomerId)->getData();
					
					$customerName = $customer['firstname'];
					$customermail = $customer['email'];
					
					$customerBillingAddress = $customer['default_billing'];
					
					
					$id = $appointment['appointment_id'];
					$description = $appointment['appointment_description'];
					$loc = $appointment['location'];
					$appointdate = $appointment['appointment_date'];
					$appointtime = $appointment['appointment_time']; 
					
					//$appStatus = $appiontment['status'];
					$deslength = strlen($description);
					
					if($deslength >= 25) {
						$des = substr($description,0,20);
						$des = $des."...";
					} else {
						$des = $description;
					}
					
					if($appointment['location']=="Customer Premises") {
						$customAddress = Mage::getModel('customer/address')->load($customerBillingAddress);
						
						$countryModel = Mage::getModel('directory/country')->loadByCode($customAddress['country_id']);
						$countryName = $countryModel->getName();
						$region_id = $customAddress['region'];
						$regionModel = Mage::getModel('directory/region')->loadByCode($region_id, $customAddress['country_id']);
						$regionName = $regionModel->getName();
						
						$customerAddress = $customAddress['street'].",".$customAddress['city'].",".$regionName.",".$countryName.",".$customAddress['postcode'];
						
						$telephone = $customAddress['telephone'];
					} else {
						$customerAddress = "Kronos AV, Unit's 8 & 9, Scotch Street Centre, Dungannon, BT701AR";
						$telephone = $appointment['telephone'];
					}
					
					// add content to the PDF
					//$this->fontBold($page);
					$page->drawText("Customer Name :", 10, 800)
						 ->drawText("Customer Email :", 10, 780)
						 ->drawText("Appointment Date :", 10, 760)
						 ->drawText("Description :", 10, 740)
						 ->drawText("Appointment Time :", 10, 720)
						 ->drawText("Location :", 10, 700)
						 ->drawText("Address :", 10, 680)
						 ->drawText("Telephone :", 10, 660);
					
					
					if($i <=0) {
						$page->drawText($customerName, $line, 800)
							 ->drawText($customermail, $line, 780)
							 ->drawText($appointdate, $line, 760)
							 ->drawText($des, $line, 740)
							 ->drawText($appointtime, $line, 720)
							 ->drawText($loc, $line, 700)
							 //->drawText($status, $line, 680)
							 ->drawText($customerAddress, $line, 680)
							 ->drawText($telephone, $line, 660);
							 
						$value = 660;
						$i++;
					} else {
						$y = $value-30;
						
						$page->drawText("Customer Name :", 10, $y)
							 ->drawText("Customer Email :", 10, $y-20)
							 ->drawText("Appointment Date :", 10, $y-40)
							 ->drawText("Description :", 10, $y-60)
							 ->drawText("Appointment Time :", 10, $y-80)
							 ->drawText("Location :", 10, $y-100)
							 ->drawText("Address :", 10, $y-120)
							 ->drawText("Telephone :", 10, $y-140);
								 
						$page->drawText($customerName, $line, $y)
							 ->drawText($customermail, $line, $y-20)
							 ->drawText($appointdate, $line, $y-40)
							 ->drawText($des, $line, $y-60)
							 ->drawText($appointtime, $line, $y-80)
							 ->drawText($loc, $line, $y-100)
							 //->drawText($status, $line, $y-120)
							 ->drawText($customerAddress, $line, $y-120)
							 ->drawText($telephone, $line, $y-140);
							 
						$value= $y -140 ;
					} 
				}
				
				// add current page to pages array
				$pdf->pages[] = $page;
				
				$pdfPath = Mage::getBaseDir('media').DS.'pdf'.DS.'appointment';
				
				if(is_dir($pdfPath)) {
					// save the document
					$pdf->save($pdfPath.DS.'appointment_export.pdf');
				} else {
					$io = new Varien_Io_File();
					$io->checkAndCreateFolder($pdfPath);
					// save the document
					$pdf->save($pdfPath.DS.'appointment_export.pdf');
				}
				
				//file location
				$fullPath = $pdfPath.DS.'appointment_export.pdf';
				
				header("Cache-Control: public");
				header("Content-Description: File Transfer");
				header('Content-disposition: attachment; filename='.basename($fullPath));
				header("Content-Type: application/pdf");
				header("Content-Transfer-Encoding: binary");
				header('Content-Length: '. filesize($fullPath));
				readfile($fullPath);
				exit;

			} catch (Zend_Pdf_Exception $e) {
				// log $e->getMessage() error
			} catch (Exception $e) {
				// log $e->getMessage() error
			}
		}
		// return document contents to be mailed
		//return $pdf->render();
		$this->_redirect('*/*/');
	}
	
	public function sendMailAction()
	{
		// create instance of Zend_Mail()
		$mail = new Zend_Mail();
		// create PDF to send
		$pdf = $this->createPdfAction();
		 
		// attach PDF and set MIME type, encoding and disposition data to handle PDF files
		$attachment = $mail->createAttachment($pdf);
		$attachment->type = 'application/pdf';
		$attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
		$attachment->encoding = Zend_Mime::ENCODING_BASE64;
		$attachment->filename = 'document.pdf';
		 
		// set basic email data including sender, receiver, message and subject line
		$mail->setBodyHtml("Your doument is attached");
		$mail->setFrom('me@mydomain.com', 'The Sender');
		$mail->addTo('you@mydomain.com', 'The Receiver');
		$mail->setSubject('A document for you');
		 
		// send email
		//$mail->send();   
	}
	public function fontBold($page)
	{
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		return $page;
	}
	/*End pdf*/
	public function newAction()
	{
		$this->_forward('newcustomer');
	}
	
	public function newcustomerAction()
	{
		$appointmentId     = $this->getRequest()->getParam('id');
		
		$customerModel  = Mage::getModel('customer/customer')->load($appointmentId);
       
		if ($customerModel->getEntityId()|| $appointmentId == 0) {
		$this->loadLayout();
			$this->_setActiveMenu('appointment/manage_appointment');
		   
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
		   
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		   
			$this->_addContent($this->getLayout()->createBlock('appointment/adminhtml_customer_edit'))
				 ->_addLeft($this->getLayout()->createBlock('appointment/adminhtml_customer_edit_tabs'));
			   
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('appointment')->__('Appointment does not exist'));
			$this->_redirect('*/*/');
		}
	}
   
	public function customersaveAction()
	{
		if( $this->getRequest()->getPost() ) 
		{  
			try {
			
				$postData = $this->getRequest()->getPost();
				
				$address = $postData['address2'];
				$firstname = $postData['firstname'];
				$lastname = $postData['lastname'];
				$emailid = $postData['email'];
				$status = $postData['status'];
				$appointment_description = $postData['appointment_description'];
				$appointment_date = $postData['appointment_date'];
				
				$appointment_time = $postData['appointment_time'];
				$place=$postData['location'];
				$appointmentId = $this->getRequest()->getParam('id');
				
				$appointmentModel = Mage::getModel('appointment/appointment')->load($appointmentId);
				
				$appointmentModel->setAppointmentDescription($postData['appointment_description'])
				->setAppointmentDate($postData['appointment_date'])	
				->setAppointmentTime($postData['appointment_time'])
				->setStatus($postData['status'])
				->setAddress($postData['address2'])
				->setLocation($postData['location'])
				->setTelephone($postData['app_telephone'])
				->save();
				
				$getCustomerId = $appointmentModel->getData('entity_id');
				
				$getCustomer = Mage::getModel('customer/customer')->load($getCustomerId);
				
				$getCustomer->setPrefix($postData['prefix'])
							->setFirstname($postData['firstname'])
							->setMiddlename($postData['middle'])
							->setLastname($postData['lastname'])
							->setSuffix($postData['suffix'])
							->save();
							
				if($place=="Customer Premises")
				{
					$customAddressData = array (
											'firstname' => $postData['firstname'],
											'lastname' => $postData['lastname'],
											'street' => array (
												'0' => $postData['street_address'],
												'1' => '',
											),
											'city' => $postData['city'],
											'region' => $postData['state'],
											'region_id' => $postData['state'],
											'postcode' => $postData['zip_code'],
											'country_id' => $postData['country_id'], 
											'telephone' => $postData['telephone'],
											'fax' => $postData['fax'],
											);
					
					$customAddress = Mage::getModel('customer/address');
					$customAddress->setData($customAddressData)
								->setCustomerId($getCustomerId)
								->setIsDefaultBilling('1')
								->setIsDefaultShipping('1')
								->setSaveInAddressBook('1');
					try {
						$customAddress->save();
					}
					catch (Exception $ex) {
						//Zend_Debug::dump($ex->getMessage());
					}	
				}
				
				if($status==2)
				{
					if($place=="In Store")
					{
							$customAddressInfo = array (
							'address' => Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('appointment_instore')->toHtml(),
							);
					}
					else{
							$customAddressInfo = array (
								'street' => array (
									'0' => $postData['street_address']
								),
								'city' => $postData['city'],
								'region' => $postData['state'],
								'region_id' => $postData['state'],
								'postcode' => $postData['zip_code'],
								'country_id' => $postData['country_id'], 
								'telephone' => $postData['telephone'],
								'fax' => $postData['fax'],
								);
					}
					
				    $this->status_email($appointment_description,$appointment_date,$appointment_time,$place,$firstname,$lastname,$emailid, $customAddressInfo);
				}
				
			   	Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Appointment was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setappointmentData(false);
 				$this->_redirect('*/*/');
				return;
				
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setappointmentData($this->getRequest()->getPost());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		$this->_redirect('*/*/');
	}
   
   public function saveAction()
   {
		if ( $this->getRequest()->getPost() ) {
			try {
			    $postData = $this->getRequest()->getPost();
				
				$address = $postData['address2'];
			    $firstname = $postData['firstname'];
				$lastname = $postData['lastname'];
				$emailid = $postData['email'];
				$appointment_description = $postData['appointment_description'];
				$appointment_date = $postData['appointment_date'];
				$appointment_time = $postData['appointment_to_time'];
				
				$place=$postData['location'];
				
				if($postData['entity_id'])
				{   
					//$getBillingInfo = Mage::getModel('customer/customer')->load($postData['entity_id'])->getData();
					
					if($place=="Customer Premises")
					{
						$customAddressData = array ('firstname' => $postData['firstname'],
												'lastname' => $postData['lastname'],
												'street' => array (
													'0' => $postData['street_address'],
													'1' => ''),
												'city' => $postData['city'],
												'region' => $postData['state'],
												'region_id' => $postData['state'],
												'postcode' => $postData['zipcode'],
												'country_id' => $postData['country_id'], 
												'telephone' => $postData['telephone'],
												'fax' => $postData['fax'],
												);
												
						$customAddress = Mage::getModel('customer/address');
						$customAddress->setData($customAddressData)
							->setCustomerId($postData['entity_id'])
							->setIsDefaultBilling('1')
							->setIsDefaultShipping('1')
							->setSaveInAddressBook('1')
							->save();							
					}
				    //$appointmentId = $this->getRequest()->getParam('id');
					
					$appointmentModel = Mage::getModel('appointment/appointment');
					$appointmentModel->setAppointmentDescription($postData['appointment_description'])
						->setAppointmentDate($postData['appointment_date'])	
						->setAppointmentTime($postData['appointment_to_time'])
						->setStatus($postData['status'])
						->setAddress($postData['address2'])
						->setLocation($postData['location'])
						->setTelephone($postData['app_telephone'])
						->setEntityId($postData['entity_id'])
						->save();
				}
				else
				{			
					$customerModel = Mage::getModel('customer/customer');
					
					$password = $customerModel->generatePassword();
								
					$customerModel->setId($this->getRequest()->getParam('id'));
					$customerModel->setfirstname($postData['firstname'])
					                    ->setWebsiteId(1)
										->setLastname($postData['lastname'])
										->setMiddlename($postData['middlename'])
										->setSuffix($postData['suffix'])
										->setPrefix($postData['prefix'])
										->setEmail($emailid)
										->setPassword($password)
										->save();
					
					if($place=="Customer Premises")
					{
						$customAddressData = array('firstname' => $postData['firstname'],
												   'lastname' => $postData['lastname'],
													'street' => array (
														'0' => $postData['street_address'],
														'1' => ''),
													'city' => $postData['city'],
													'region' => $postData['state'],
													'region_id' => $postData['state'],
													'postcode' => $postData['zipcode'],
													'country_id' => $postData['country_id'], 
													'telephone' => $postData['telephone'],
													'fax' => $postData['fax'],
													);
					
						$customAddress = Mage::getModel('customer/address');
						$customAddress->setData($customAddressData)
								->setCustomerId($customerModel->getId())
								->setIsDefaultBilling('1')
								->setIsDefaultShipping('1')
								->setSaveInAddressBook('1')
								->save();
					}
					
					$entityid=$customerModel->getEntityId();
					
					$appointmentModel = Mage::getModel('appointment/appointment');
					
					$appointmentModel->setId($this->getRequest()->getParam('id'))
						->setAppointmentDescription($postData['appointment_description'])
						->setAppointmentDate($postData['appointment_date'])	
						->setAppointmentTime($postData['appointment_to_time'])
						->setStatus($postData['status'])
						->setAddress($postData['address2'])
						->setLocation($postData['location'])
						->setTelephone($postData['app_telephone'])
						->setEntityId($entityid)
						->save();
						
					$firstname=$postData['firstname'];
					$lastname=$postData['lastname'];
					$emailid=$postData['email'];
					$this->newcustomeremail($firstname,$lastname,$emailid,$password);
				}
				
				if($place=="In Store")
				{
					$customerAddressData = array (
							'address' => Mage::app()->getLayout()->createBlock('cms/block')->setBlockId('appointment_instore')->toHtml(),
						);
				}
				else{
					
					$customerAddressData = array (
						'street' => array (
							'0' => $postData['street_address']
						),
						'city' => $postData['city'],
						'region' => $postData['state'],
						'region_id' => $postData['state'],
						'postcode' => $postData['zipcode'],
						'country_id' => $postData['country_id'], 
						'telephone' => $postData['telephone'],
						'fax' => $postData['fax'],
					);
				}	
				
				$this->newappointmentemail($appointment_description,$appointment_date,$appointment_time,$place,$firstname,$lastname,$emailid, $customerAddressData);
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Appointment was successfully saved'));
				
				Mage::getSingleton('adminhtml/session')->setappointmentData(false);
 				$this->_redirect('*/*/');
				return;
			} 
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setappointmentData($this->getRequest()->getPost());
				$this->_redirect('*/*/', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function deleteAction()
	{
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$appointmentModel = Mage::getModel('appointment/appointment');
			   
				$appointmentModel->setId($this->getRequest()->getParam('id'))
					->delete();
				   
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('appointment_id')));
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function massDeleteAction()
	{
		$appointmentIds = $this->getRequest()->getParam('id');
		
			if ($this->getRequest()->getParam('id')>0) {
			try {
				 foreach ($appointmentIds as $appointmentId) { 
						$appointmentModel = Mage::getModel('appointment/appointment')->load($appointmentId);	
						$appointmentModel->delete();
					}
				   
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Appointment was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	
	/**
	 * Product grid for AJAX request.
	 * Sort and filter result for example.
	 */
	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			   $this->getLayout()->createBlock('appointment/adminhtml_appointment_grid')->toHtml()
		);
	}
	
	public function customergridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			   $this->getLayout()->createBlock('appointment/adminhtml_customer_grid')->toHtml()
		);
	}
	
	public function customerAction()
	{
		$this->loadLayout()->_setActiveMenu('appointment/manage_appointment');
		$this->_addContent($this->getLayout()->createBlock('appointment/adminhtml_customer'));
		$this->renderLayout();
	}
	
	public function newcustomeremail($firstname,$lastname,$emailid,$password)
	{
	  
		 $emailTemplate = Mage::getModel('core/email_template')->loadDefault('new_customer_email_template');

		 //Getting the Store E-Mail Sender Name.
		 $senderName = Mage::getStoreConfig('trans_email/ident_general/name');

		 //Getting the Store General E-Mail.
		 $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');

		
		$emailLink = Mage::getBaseUrl().'customer/account/login';
		
		 //Variables for Twitter Confirmation Mail.
		 $emailTemplateVariables = array();
		 $emailTemplateVariables['name'] = $firstname.' '.$lastname;
		 $emailTemplateVariables['email'] = $emailid;
		 $emailTemplateVariables['password'] = $password;
		 $emailTemplateVariables['accounturl'] = $emailLink;

		 //Appending the Custom Variables to Template.
		 $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);

		 //Sending E-Mail to Customers.
		 $mail = Mage::getModel('core/email')
		 ->setToName($firstname)
		 ->setToEmail($emailid)
		 ->setBody($processedTemplate)
		 ->setSubject('Account confirmation for '.$firstname.' '.$lastname)
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
	
	public function newappointmentemail($appointment_description,$appointment_date,$appointment_time,$place,$firstname,$lastname,$emailid, $customAddress)
	{		 
		$emailTemplate = Mage::getModel('core/email_template')->loadDefault('appointment_confirm_email_template');

		//Getting the Store E-Mail Sender Name.
		$senderName = Mage::getStoreConfig('trans_email/ident_general/name');

		//Getting the Store General E-Mail.
		$senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
		
		 //Variables for Twitter Confirmation Mail.
		$emailTemplateVariables = array();
		$emailTemplateVariables['name'] = $firstname.' '.$lastname;
		$emailTemplateVariables['email'] = $emailid;
		$emailTemplateVariables['appointment_description'] = $appointment_description;
		$emailTemplateVariables['appointment_date'] = $appointment_date;
		$emailTemplateVariables['appointment_time'] = $appointment_time;
		$emailTemplateVariables['place'] = $place;
		
		if($place=="In Store")
		{
			$emailTemplateVariables['address'] = $customAddress['address'];
		}
		else
		{
			$country_id =$customAddress['country_id'];
			$countryModel = Mage::getModel('directory/country')->loadByCode($customAddress['country_id']);
			$countryName = $countryModel->getName();
			$region_id = $customAddress['region'];
			$regionModel = Mage::getModel('directory/region')->loadByCode($region_id, $country_id);
			$regionName = $regionModel->getName();
			
			$emailTemplateVariables['address'] = $customAddress['street'][0].'<br />'.$customAddress['city'].'<br />'.$regionName.'<br />'.$countryName.'<br />'.$customAddress['postcode'].'<br />'.$customAddress['telephone'].'<br />'.$customAddress['fax'];
		}	
		
		//Appending the Custom Variables to Template.
		$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);

		//Sending E-Mail to Customers.
		$mail = Mage::getModel('core/email')
		 ->setToName($firstname)
		 ->setToEmail($emailid)
		 ->setBody($processedTemplate)
		 ->setSubject('New Appointment Details')
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
	
	public function status_email($appointment_description, $appointment_date, $appointment_time, $place, $firstname,$lastname,$emailid, $customAddressInfo)
	{
		    
		$emailTemplate = Mage::getModel('core/email_template')->loadDefault('status_email_template');

		//Getting the Store E-Mail Sender Name.
		$senderName = Mage::getStoreConfig('trans_email/ident_general/name');

		//Getting the Store General E-Mail.
		$senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
		
		//Variables for Twitter Confirmation Mail.
		$emailTemplateVariables = array();
		$emailTemplateVariables['name'] = $firstname.' '.$lastname;
		$emailTemplateVariables['email'] = $emailid;
		$emailTemplateVariables['appointment_description'] = $appointment_description;
		$emailTemplateVariables['appointment_date'] = $appointment_date;
		$emailTemplateVariables['appointment_time'] = $appointment_time;
		$emailTemplateVariables['place'] = $place;
		
		if($place=="In Store")
		{
			$emailTemplateVariables['address'] = $customAddressInfo['address'];
		}
		else
		{
			$countryModel = Mage::getModel('directory/country')->loadByCode($customAddressInfo['country_id']);

			$countryName = $countryModel->getName();
			$country_id = $customAddressInfo['country_id'];
			$region_id = $customAddressInfo['region'];
			$regionModel = Mage::getModel('directory/region')->loadByCode($region_id, $country_id);
			$regionName = $regionModel->getName();
			
			
			$emailTemplateVariables['address'] = $customAddressInfo['street'][0].'<br />'.$customAddressInfo['city'].'<br />'. $regionName .'<br />'.$countryName.'<br />'.$customAddressInfo['postcode'].'<br />'.$customAddressInfo['telephone'].'<br />'.$customAddressInfo['fax'];
		}	
		
		 

		 //Appending the Custom Variables to Template.
		 $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);

		 //Sending E-Mail to Customers.
		 $mail = Mage::getModel('core/email')
		 ->setToName($firstname)
		 ->setToEmail($emailid)
		 ->setBody($processedTemplate)
		 ->setSubject('Thanks for meeting us')
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
	
	public function stateAction() 
	{
        $countrycode = $this->getRequest()->getParam('country');
        
		$statearray = Mage::getModel('directory/region')->getResourceCollection() ->addCountryFilter($countrycode)->load();
	
		$getDataNew = $statearray->getData();
		
		if(empty($getDataNew))
		{
			$state = "<input type='text' class='required-entry input-text required-entry validation-passed' value='' name='state' id='state'>";
		}
		else
		{
			$state = "<select name='state'><option value=''>Please Select</option>";
            
            foreach ($statearray as $_state) {
                $state .= "<option value='" . $_state->getCode() . "'>" . $_state->getDefaultName() . "</option>";
            }
			$state .= "</select>";
		}
		
        echo $state;
    }
	
}

