<?php
class Kronosav_Repair_RepairController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->_setActiveMenu('repair');
		$this->_addContent($this->getLayout()->createBlock('repair/adminhtml_repair'));
		$this->renderLayout();
	}

	public function repairgridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('repair/adminhtml_repair_grid')->toHtml()
		);
	}
	
	public function customergridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			   $this->getLayout()->createBlock('repair/adminhtml_customer_grid')->toHtml()
		);
	}
	
	public function editAction()
	{
		$repairId     = $this->getRequest()->getParam('id');
		
		$repairModel  = Mage::getModel('repair/repair')->load($repairId);
		$entityid=$repairModel->getEntityId();
		$customerModel = Mage::getModel('customer/customer')->load($entityid);
		
		if ($repairModel->getId() || $repairId == 0) {
 
			Mage::register('repairedit_data', $repairModel);
			Mage::register('customeredit_data', $customerModel);
			$this->loadLayout();		   
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('repair/adminhtml_repair_editold'))
				 ->_addLeft($this->getLayout()->createBlock('repair/adminhtml_repair_edit_tabs'));			   
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('repair')->__('Repair Log does not exist'));
			$this->_redirect('*/*/');
		}
	}
	public function newAction()
    {
		$this->loadLayout();
		$this->_setActiveMenu('repair');
		$this->_addContent($this->getLayout()->createBlock('repair/adminhtml_customer'));
		$this->renderLayout();
	}
	
	public function customereditAction()
	{
		// $this->_setActiveMenu('repair');
		$customerId     = $this->getRequest()->getParam('id');
		
		$customerModel = Mage::getModel('customer/customer')->load($customerId);
		
		if ($customerModel->getId() || $customerId == 0) {

			Mage::register('customeredit_data', $customerModel);
			$this->loadLayout();		   
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('repair/adminhtml_customer_edit'))
				 ->_addLeft($this->getLayout()->createBlock('repair/adminhtml_customer_edit_tabs'));			   
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('repair')->__('Customer does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function selectAction()
	{
		$CustomerId = $this->getRequest()->getParam('id');
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('repair/adminhtml_repair'));
		$this->renderLayout();
	}
	
	public function newcustomerAction()
	{
		
		$repairId = $this->getRequest()->getParam($repairId);
		$customerModel = Mage::getModel('customer/customer')->load($repairId);
		if($customerModel->getEntityId() || $repairId == 0)
		{
			$this->loadLayout();
			$this->_setActiveMenu('repair');
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('repair/adminhtml_customer_edit'))
				 ->_addLeft($this->getLayout()->createBlock('repair/adminhtml_customer_edit_tabs'));			   
			$this->renderLayout();
		}
		else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('loan')->__('Loan does not exist'));
			$this->_redirect('*/*/');
		}
	}
	
	public function customersaveAction()
	{
	 
		if ( $this->getRequest()->getPost() ) 
		{
		   try {
				$postData = $this->getRequest()->getPost();
				$firstname=$postData['first_name'];
				$lastname=$postData['last_name'];
				$middlename=$postData['middle_name'];
				$email=$postData['email'];
				$status=$postData['status'];
				$street_address=$postData['street_address'];
				$city=$postData['city'];
				$country=$postData['country'];
				$zip_code=$postData['zip_code'];
				$telephone=$postData['telephone'];
				$fax=$postData['fax'];
				$deposit_amount=$postData['deposit_amount'];
				$product_make=$postData['product_make'];
				$product_model=$postData['product_model'];
				$serial_no=$postData['serial_no'];
				$repair_details=$postData['repair_details'];
				$repair_submission_date=$postData['repair_submission_date'];
				$repair_cost=$postData['repair_cost'];
				$repair_time=$postData['repair_time'];
				$diagnostic_description=$postData['diagnostic_description'];
				$entity_id=$postData['entity_id'];
				$received_date = date("Y-m-d");
				$updated_date = date("Y-m-d");
	
				if($entity_id)
				{
					$collection = Mage::getModel('repair/repair')->load($repair_Id)
								->setProductMake($product_make)
								->setProductModel($product_model)
								->setSerialNo($serial_no)
								->setStatus($status)
								->setEntityId($entity_id)
								->setRepairSubmissionDate($repair_submission_date)
								->setRepairCost($repair_cost)
								->setRepairTime($repair_time)
								->setRepairDetails($repair_details)
								->setDepositAmount($deposit_amount)
								->setDiagnosticDesc($diagnostic_description)
								->setReceivedDate($received_date)
								->setUpdatedDate($updated_date)
								->save();						
				}
				if(!($entity_id)){
					$customerModel = Mage::getModel('customer/customer');
					$password=$customerModel->generatePassword();								
					$customerModel->setId($this->getRequest()->getParam('id'));
					$entity_id = $customerModel->setFirstname($firstname)
									->setWebsiteId(1)
									->setLastname($lastname)
									->setMiddlename($middlename)
									->setEmail($email)	
									->setPassword($password)
									->save()->getId();
					
					$customAddressData = array (
										'firstname' => $postData['first_name'],
										'lastname' => $postData['last_name'],
										'street' => array (
										'0' => $postData['street_address'],
										'1' => '',
										),
										'city' => $postData['city'],
										'region' => $postData['state'],
										'region_id' => $postData['state'],
										'postcode' => $postData['zip_code'],
										'country_id' => $postData['country'], 
										'telephone' => $postData['telephone'],
										'fax' => $postData['fax'],
										);
					$customAddress = Mage::getModel('customer/address');
					$customAddress->setData($customAddressData)
								->setCustomerId($entity_id)
								->setIsDefaultBilling('1')
								->setIsDefaultShipping('1')
								->setSaveInAddressBook('1')
								->save();
					$collection = Mage::getModel('repair/repair')->load($repair_Id)
										->setProductMake($product_make)
										->setProductModel($product_model)
										->setSerialNo($serial_no)
										->setStatus($status)
										->setEntityId($entity_id)
										->setRepairSubmissionDate($repair_submission_date)
										->setDepositAmount($deposit_amount)
										->setRepairCost($repair_cost)
										->setRepairTime($repair_time)
										->setRepairDetails($repair_details)
										->setDiagnosticDesc($diagnostic_description)
										->setReceivedDate($received_date)
										->setUpdatedDate($updated_date)
										->save();
					}
		//Getting the Store E-Mail Sender Name.
		 $senderName = Mage::getStoreConfig('trans_email/ident_general/name');

		 //Getting the Store General E-Mail.
		 $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
		 $subject = "Your Product Repair Details";
		 $emailTemplate = Mage::getModel('core/email_template')->loadDefault('new_repair_email_template');
		 
		 $emailTemplateVariables = array();
		 $emailTemplateVariables['deposit_amount'] = $deposit_amount;
		 $emailTemplateVariables['customer_name'] = $firstname;
		 $emailTemplateVariables['product_make'] = $product_make;
		 $emailTemplateVariables['product_model'] = $product_model;
		 $emailTemplateVariables['serial_no']=$serial_no;
		 $emailTemplateVariables['repair_details'] = $repair_details;
		 $emailTemplateVariables['repair_submission_date'] = $repair_submission_date;

		 //Appending the Custom Variables to Template.
		 $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
		 $this->sendmail($firstname,$email,$processedTemplate,$subject,$senderEmail,$senderName); 			
			   	Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Repair was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setloanData(false);
 				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setloanData($this->getRequest()->getPost());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function saveAction()
	{
		$postData = $this->getRequest()->getPost();
		if ( $this->getRequest()->getPost() ) 
		{
		   
			try {
			
				$postData = $this->getRequest()->getPost();
				$firstname=$postData['first_name'];
				$lastname=$postData['last_name'];
				$middlename=$postData['middle_name'];
				$email=$postData['email'];
				$status=$postData['status'];
				$street_address=$postData['street_address'];
				$city=$postData['city'];
				$country=$postData['country'];
				$zip_code=$postData['zip_code'];
				$telephone=$postData['telephone'];
				$fax=$postData['fax'];
				$deposit_amount=$postData['deposit_amount'];
				$product_make=$postData['product_make'];
				$product_model=$postData['product_model'];
				$serial_no=$postData['serial_no'];
				$repair_details=$postData['repair_details'];
				$repair_submission_date=$postData['repair_submission_date'];
				$repair_cost=$postData['repair_cost'];
				$repair_time=$postData['repair_time'];
				$diagnostic_description=$postData['diagnostic_description'];
				$miscellaneous_details=$postData['miscellaneous_details'];
				$entity_id=$postData['entity_id'];
				$updated_date = date("Y-m-d");
				
						$collection = Mage::getModel('repair/repair')->load($postData['repair_id'])
										->setProductMake($product_make)
										->setProductModel($product_model)
										->setSerialNo($serial_no)
										->setStatus($status)
										->setEntityId($entity_id)
										->setRepairSubmissionDate($repair_submission_date)
										->setRepairCost($repair_cost)
										->setRepairTime($repair_time)
										->setRepairDetails($repair_details)
										->setDiagnosticDesc($diagnostic_description)
										->setMiscellaneousDetails($miscellaneous_details)
										->setDepositAmount($deposit_amount)
										->setUpdatedDate($updated_date)
										->save();
										// echo $var = $collection ->save()->getId();exit;
						$customAddressData = array (
											'firstname' => $postData['first_name'],
											'lastname' => $postData['last_name'],
											'street' => array (
												'0' => $postData['street_address'],
												'1' => '',
											),
											'city' => $postData['city'],
											'region' => $postData['state'],
											'region_id' => $postData['state'],
											'postcode' => $postData['zip_code'],
											'country_id' => $postData['country'], 
											'telephone' => $postData['telephone'],
											'fax' => $postData['fax'],
											);
					$customAddress = Mage::getModel('customer/address');
					$customAddress->setData($customAddressData)
								->setCustomerId($entity_id)
								->setIsDefaultBilling('1')
								->setIsDefaultShipping('1')
								->setSaveInAddressBook('1')
								->save();
		
		if(($repair_cost!=0)&&($status==0))
		{	
			//Send Confirmation mail
			//Getting the Store E-Mail Sender Name.
			 $senderName = Mage::getStoreConfig('trans_email/ident_general/name');

			 //Getting the Store General E-Mail.
			 $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
			 $subject = "Email Confirmation";
			 $emailTemplate = Mage::getModel('core/email_template')->loadDefault('repair_confirm_email_template');
			 $emailTemplateVariables = array();
			 $emailTemplateVariables['customer_name'] = $firstname;
			 $emailTemplateVariables['deposit_amount'] = $deposit_amount;
			 $emailTemplateVariables['product_make'] = $product_make;
			 $emailTemplateVariables['product_model'] = $product_model;
			 $emailTemplateVariables['serial_no']=$serial_no;
			 $emailTemplateVariables['repair_details'] = $repair_details;
			 $emailTemplateVariables['repair_cost'] = $repair_cost;
			 $emailTemplateVariables['repair_time'] = $repair_time;
			 $emailTemplateVariables['diagnostic_description'] = $diagnostic_description;
			 $emailTemplateVariables['repair_submission_date'] = $repair_submission_date;
			 $emailTemplateVariables['yes_url'] = Mage::getUrl('repair/confirm/yes',array('repair_id'=>$collection->getId()));
			 $emailTemplateVariables['no_url'] = Mage::getUrl('repair/confirm/no',array('repair_id'=>$collection->getId()));
			 $emailTemplateVariables['repair_submission_date'] = $repair_submission_date;
			 
			 $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			 $this->sendmail($firstname,$email,$processedTemplate,$subject,$senderEmail,$senderName); 
		}
		if($status==2)
		{
			//Delivery Email
			//Getting the Store E-Mail Sender Name.
			 $senderName = Mage::getStoreConfig('trans_email/ident_general/name');

			 //Getting the Store General E-Mail.
			 $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
			 $subject = "Product ready for delivery";
			 $emailTemplate = Mage::getModel('core/email_template')->loadDefault('delivery_email_template');
			 
			 $emailTemplateVariables = array();
			 $emailTemplateVariables['customer_name'] = $firstname;
			 $emailTemplateVariables['deposit_amount'] = $deposit_amount;
			 $emailTemplateVariables['product_make'] = $product_make;
			 $emailTemplateVariables['product_model'] = $product_model;
			 $emailTemplateVariables['serial_no']=$serial_no;
			 $emailTemplateVariables['repair_details'] = $repair_details;
			 $emailTemplateVariables['repair_cost'] = $repair_cost;
			 $emailTemplateVariables['repair_time'] = $repair_time;
			 $emailTemplateVariables['diagnostic_description'] = $diagnostic_description;
			 $emailTemplateVariables['repair_submission_date'] = $repair_submission_date;

			 //Appending the Custom Variables to Template.
			 $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			 $this->sendmail($firstname,$email,$processedTemplate,$subject,$senderEmail,$senderName); 
		 }
				
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Repair was successfully saved'));
			Mage::getSingleton('adminhtml/session')->setloanData(false);
			$this->_redirect('*/*/');
			return;
			} catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			Mage::getSingleton('adminhtml/session')->setloanData($this->getRequest()->getPost());
			$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			return;
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function massDeleteAction()
	{
		$repairIds = $this->getRequest()->getParam('id');
			if ($this->getRequest()->getParam('id')>0) {
			try {
				 foreach ($repairIds as $repairId) { 
						$repairModel = Mage::getModel('repair/repair')->load($repairId);	
						$repairModel->delete();
					}
				   
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('repair was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	public function sendmail($firstname,$emailid,$processedTemplate,$subject,$senderEmail,$senderName)
	{
		// echo "-------------------------------------------";
		// echo $firstname.$emailid.$processedTemplate.$subject.$senderEmail.$senderName;exit;
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
	
	protected function insertImageIn($imagename)
	{
		if (!is_null($imagename)) {
			try{
				$image = Mage::getConfig()->getOptions()->getMediaDir().DS.'repair'.DS.$imagename;					
				return $image1 = Zend_Pdf_Image::imageWithPath($image);
			}
			catch (Exception $e) {
				return false;
			}
		}
	}
	
	
	
	// PDF Start
	public function createPdfAction()
	{
		
		// create instance of Zend_Pdf()
		$pdf = new Zend_Pdf();
		$repairIds = $this->getRequest()->getParam('id');
		
		if ($this->getRequest()->getParam('id') > 0) {
			try {
				
				$i = 0;
				
				foreach($repairIds as $repairId) { 
						
					$page = $this->newpage($repairId);	
					$pdf->pages[] = $page;
				}
				
				// add current page to pages array
				
				
				$pdfPath = Mage::getBaseDir('media').DS.'pdf'.DS.'repair';
				
				if(is_dir($pdfPath)) {
					// save the document
					$pdf->save($pdfPath.DS.'repair_export.pdf');
				} else {
					$io = new Varien_Io_File();
					$io->checkAndCreateFolder($pdfPath);
					// save the document
					$pdf->save($pdfPath.DS.'repair_export.pdf');
				}
				
				// file location
				$fullPath = $pdfPath.DS.'repair_export.pdf';
				
				header("Cache-Control: public");
				header("Content-Description: File Transfer");
				header('Content-disposition: attachment; filename='.basename($fullPath));
				header("Content-Type: application/pdf");
				header("Content-Transfer-Encoding: binary");
				header('Content-Length: '. filesize($fullPath));
				readfile($fullPath);
				exit;

			} catch (Zend_Pdf_Exception $e) {
				Mage::logException($e);
			} catch (Exception $e) {
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function newpage($repairId)
	{
		$line = 120;
	
		// set new page, page size and font
		$page = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
		$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
		
		$page->setFont($font, 10);
		$repairModel = Mage::getModel('repair/repair')->load($repairId);	
					
		$customerId = $repairModel->getEntityId();
		$customer = Mage::getModel('customer/customer')->load($customerId);
		$address_id = $customer->getDefaultBilling();
		$address = Mage::getModel('customer/address')->load($address_id);
	
		//Customer Details
		$customerName = $customer->getName();
		$customerFirstname = $customer->getFirstname();
		$customerMiddlename = $customer->getMiddlename();
		$customerLastname = $customer->getLastname();
		$customermail = $customer->getEmail();
		$street = $address->getStreet();
		$city = $address->getCity();
		$country = $address->getCountryId();
		$state = $address->getRegion();
		$zipcode = $address->getPostcode();
		$telephone = $address->getTelephone();
		$fax = $address->getFax();
		
		//Message
		$title = "What Happens Now?";
		$message1 = "1. Our Enginner's with analysis the fault and 	forward you and email with fault description and cost to repair";
		$squ = "'";
		$message2 = '2. If you decide to continue with the repair then simply click "Yes I'.$squ.'d like to continue with the repair" if you decide you don'.$squ.'t want to repair the unit then click the "No I'.$squ.'d like to cancel the repair"';
		$message3 = "3. Once the item is ready for collection you wil receive an email to notify you of this.";
		$this->textWrap($page,$message1,300,300);
		$this->textWrap($page,$message2,300,260);
		$this->textWrap($page,$message3,300,200);
		//Repair Details
		$id = $repairModel->getRepairId();
		$depositamt = $repairModel->getDepositAmount();
		$repairdetails = $repairModel->getRepairDetails();
		$productMake = $repairModel->getProductMake();
		$productModel = $repairModel->getProductModel();
		$receivedDate = $repairModel->getReceivedDate();
		$updatedDate = $repairModel->getUpdatedDate();
		$serialNo = $repairModel->getSerialNo(); 
		$imagebarcode = $this->insertImageIn('barcode.jpg');
		$storelogo = $this->insertImageIn('logo.jpg');
		$faultimage = $this->insertImageIn('fault.jpg');
		$repairimage = $this->insertImageIn('repair.jpg');
		$speaker = $this->insertImageIn('speaker.jpg');
		$hifi = $this->insertImageIn('hifi.jpg');
		$kronosav = $this->insertImageIn('kronos.jpg');
		$speakertext = $this->insertImageIn('speakertext.jpg');
		$page->drawImage($imagebarcode, 200, 800, 300, 760);
		$repairNo = 'RP'.substr(number_format(time() * rand(), 0, '', ''), 0, 10);
		$page->drawText($repairNo, 215, 750);
		$page->drawImage($storelogo, 400, 720, 500, 800);
		$page->drawImage($repairimage, 380, 540, 500, 620);
		$page->drawImage($speaker, 200, 540, 300, 680);
		$page->drawImage($hifi, 10, 540 , 110, 600);
		$page->drawImage($kronosav, 320, 360 , 520, 420);
		$page->drawImage($speakertext, 420, 40 , 580, 180);
		$page->drawImage($imagebarcode, 200, 50, 300, 100);
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(1));
		$page->drawRoundedRectangle(300, 630, 480, 720,10);
		$page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
		$page->drawText("Fault:", 310, 700);
		//$page->drawText($repairdetails, 310, 680);
		$this->textWrap($page,$repairdetails,310,680,40);
		$page->drawText($repairNo, 215, 40);
		// add content to the PDF
		$page->drawText('Customer Name'.str_repeat(' ', 2).':', 10, 720)
			 ->drawText('Customer Email'.str_repeat(' ', 2).':', 10, 700)
			 ->drawText('Serial No'.str_repeat(' ', 13).':', 10, 680)
			 ->drawText('Product Make'.str_repeat(' ', 5).':', 10, 660)
			 ->drawText('Product Model'.str_repeat(' ', 4).':', 10, 640)
			 ->drawText('Received Date'.str_repeat(' ', 4).':', 10, 620)
			 ->drawText('Updated Time'.str_repeat(' ', 5).':', 10, 600)
			 ->drawText('First Name', 10, 400)
			 ->drawText('Middle Name', 10, 380)
			 ->drawText('Last Name', 10, 360)
			 ->drawText('Email', 10, 340)
			 ->drawText('Street Address', 10, 320)
			 ->drawText('City', 10, 300)
			 ->drawText('Country', 10, 280)
			 ->drawText('State', 10, 260)
			 ->drawText('Zip code', 10, 240)
			 ->drawText('Telephone', 10, 220)
			 ->drawText('Fax', 10, 200)
			 ->drawText('Desposit Amount', 10, 180)
			 ->drawText('Product Make', 10, 160)
			 ->drawText('Product Model', 10, 140)
			 ->drawText('Serial Number', 10, 120)
			 ->drawText('Repair Details', 10, 100)
			 ->drawText('Repair Submission Date', 10, 80);
					
		
			$page->drawText($customerName, $line, 720)
				 ->drawText($customermail, $line, 700)
				 ->drawText($serialNo, $line, 680)
				 ->drawText($productMake, $line, 660)
				 ->drawText($productModel, $line, 640)
				 ->drawText($receivedDate, $line, 620)
				 ->drawText($updatedDate, $line, 600)
				 ->drawText(': '.$customerFirstname, $line, 400)
				 ->drawText(': '.$customerMiddlename, $line, 380)
				 ->drawText(': '.$customerLastname, $line, 360)
				 ->drawText(': '.$customermail, $line, 340)
				 ->drawText(': '.$street[0].' '.$street[1], $line, 320)
				 ->drawText(': '.$city, $line, 300)
				 ->drawText(': '.$country, $line, 280)
				 ->drawText(': '.$state, $line, 260)
				 ->drawText(': '.$zipcode, $line, 240)
				 ->drawText(': '.$telephone, $line, 220)
				 ->drawText(': '.$fax, $line, 200)
				 ->drawText(': '.$depositamt, $line, 180)
				 ->drawText(': '.$productMake, $line, 160)
				 ->drawText(': '.$productModel, $line, 140)
				 ->drawText(': '.$serialNo, $line, 120)
				 ->drawText(': '.$repairdetails, $line, 100)
				 ->drawText(': '.$receivedDate, $line+20, 80);
			$page->setFont($font,14);
			$page->drawText($title, 350, 340);
			
		 
		return $page;
							 
	}
	
	public function textWrap($page,$message,$start,$line,$limit=60)
	{
		$textChunk = wordwrap($message, $limit, "\n");
		foreach(explode("\n", $textChunk) as $textLine){
		  if ($textLine!=='') {
			$page->drawText(strip_tags(ltrim($textLine)), $start, $line, 'UTF-8');
			$line -=14;
		  }
		}
	}
}
