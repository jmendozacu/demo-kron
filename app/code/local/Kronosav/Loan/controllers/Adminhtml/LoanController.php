<?php 
class Kronosav_Loan_Adminhtml_LoanController extends Mage_Adminhtml_Controller_Action
{ 
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('loan/manage_loan')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		return $this;
	}      
	public function indexAction() 
	{
    	$this->_initAction();       
		$this->_addContent($this->getLayout()->createBlock('loan/adminhtml_loan'));
		$this->renderLayout();
	} 
	public function editAction()
	{
		$loanId     = $this->getRequest()->getParam('id');
		
		$loanModel  = Mage::getModel('loan/loan')->load($loanId);
		$entityid = $loanModel->getEntityId();
		$customerModel = Mage::getModel('customer/customer')->load($entityid);
		
		if ($loanModel->getId() || $loanId == 0) {
 
			Mage::register('loanedit_data', $loanModel);
		
			Mage::register('customeredit_data', $customerModel);
 
			$this->loadLayout();
			$this->_setActiveMenu('loan/manage_loan');
		   
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
		   
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		   
			$this->_addContent($this->getLayout()->createBlock('loan/adminhtml_loan_edit'))
				 ->_addLeft($this->getLayout()->createBlock('loan/adminhtml_loan_edit_tabs'));
			   
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('loan')->__('Loan does not exist'));
			$this->_redirect('*/*/');
		}
	}
	public function createAction()
	{
		$ids = Mage::getSingleton('adminhtml/session')->getProductDetails();
		if($ids)
		{
			$loanId = $this->getRequest()->getParam('id');
					
			$customerModel  = Mage::getModel('customer/customer')->load($loanId);
			
			if ($customerModel->getEntityId()|| $loanId == 0) {

				Mage::register('customeredit_data', $customerModel);
				
				$this->loadLayout();
				$this->_setActiveMenu('loan/manage_loan');
			   
				$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
				$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
			   
				$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			   
				$this->_addContent($this->getLayout()->createBlock('loan/adminhtml_customer_edit'))
					 ->_addLeft($this->getLayout()->createBlock('loan/adminhtml_customer_edit_tabs'));
				   
				$this->renderLayout();
			} else {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('loan')->__('Loan does not exist'));
				$this->_redirect('*/*/');
			}
		}
		else
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('loan')->__('Add atleast one product'));
			$this->_redirect('*/*/select');
			
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
		$loanIds = $this->getRequest()->getParam('id');
		
		if ($this->getRequest()->getParam('id')>0) {
		try {
			
			$i = 0;
			foreach ($loanIds as $loanId) 
			{ 
				$loanModel = Mage::getModel('loan/loan')->load($loanId);	
				$loan = $loanModel->getData();
				$getCustomerId = $loan['entity_id'];
				$customer = Mage::getModel('customer/customer')->load($getCustomerId)->getData();
				$customerName = $customer['firstname'];
				$customermail = $customer['email'];
				
				$customerBillingAddress = $customer['default_billing'];
					
				$id = $loan['loan_id'];
				$description = $loan['loan_description'];
				$loanFromDate = $loan['loan_from_date'];
				$loanToDate = $loan['loan_to_date'];
				$deposit = $loan['deposit_amount']; 
				
				//$appStatus = $appiontment['status'];
				
			
				$customAddress = Mage::getModel('customer/address')->load($customerBillingAddress);
				
				$countryModel = Mage::getModel('directory/country')->loadByCode($customAddress['country_id']);
				$countryName = $countryModel->getName();
				$region_id = $customAddress['region'];
				$regionModel = Mage::getModel('directory/region')->loadByCode($region_id, $customAddress['country_id']);
				$regionName = $regionModel->getName();
				
				$customerAddress = $customAddress['street'].",".$customAddress['city'].",".$regionName.",".$countryName.",".$customAddress['postcode'];
				
			$telephone = $customAddress['telephone'];
			 $Names = array();
			$loanProducts = Mage::getModel('loan/product')->getCollection()
							->addFieldToFilter("loan_id",$id);
			$selectedProducts = $loanProducts->getData(); 
			foreach($selectedProducts as $product)
			{
				$productId = $product['product_id'];
				$collection=Mage::getModel('catalog/product')->load($productId);
		        $Names[]=$collection->getData('name');
			}
			
			$productName = implode(",",$Names);
			$productlength = strlen($productName);
				
				if($productlength >= 80)
				{
					$products = substr($productName,0,75);
					$products = $products."...";
				}
				else 
				{
					$products = $productName;
				}
			// add content to the PDF
			//$this->fontBold($page);
			$page->drawText("Customer Name :", 10, 800)
				 ->drawText("Customer Email :", 10, 780)
				 ->drawText("Description :", 10, 760)
				 ->drawText("Loan From Date :", 10, 740)
				 ->drawText("Loan To Date:", 10, 720)
				 ->drawText("Deposit Amount:", 10, 700)
				 ->drawText("Address :", 10, 680)
				 ->drawText("Telephone :", 10, 660)
				 ->drawText("Products :", 10, 640);
		
			 if($i <=0)
			 {
				$page->drawText($customerName, $line, 800)
					 ->drawText($customermail, $line, 780)
					 ->drawText($description, $line, 760)
					 ->drawText($loanFromDate, $line, 740)
					 ->drawText($loanToDate, $line, 720)
					 ->drawText($deposit, $line, 700)
					 ->drawText($customerAddress, $line, 680)
					 ->drawText($telephone, $line, 660)
					 ->drawText($products, $line, 640);
					 
				$value = 640;
				 $i++;
			 }
			 else
			 {
				$y = $value-30;
				
				$page->drawText("Customer Name :", 10, $y)
					 ->drawText("Customer Email :", 10, $y-20)
					 ->drawText("Description :", 10, $y-40)
					 ->drawText("Loan From Date :", 10, $y-60)
					 ->drawText("Loan To Date :", 10, $y-80)
					 ->drawText("Deposit Amount :", 10, $y-100)
					 ->drawText("Address :", 10, $y-120)
					 ->drawText("Telephone :", 10, $y-140)
					 ->drawText("Products :", 10, $y-160);
						 
				$page->drawText($customerName, $line, $y)
					 ->drawText($customermail, $line, $y-20)
					 ->drawText($description, $line, $y-40)
					 ->drawText($loanFromDate, $line, $y-60)
					 ->drawText($loanToDate, $line, $y-80)
					 ->drawText($deposit, $line, $y-100)
					 ->drawText($customerAddress, $line, $y-120)
					 ->drawText($telephone, $line, $y-140)
					 ->drawText($products, $line, $y-160);
					 
				$value= $y -160 ;
			 } 
		}
		
		// add current page to pages array
		$pdf->pages[] = $page;
		
		$pdfPath = Mage::getBaseDir('media').DS.'pdf'.DS.'loan';
		
		if(is_dir($pdfPath)) {
			// save the document
			$pdf->save($pdfPath.DS.'loan_export.pdf');
		} else {
			$io = new Varien_Io_File();
			$io->checkAndCreateFolder($pdfPath);
			// save the document
			$pdf->save($pdfPath.DS.'loan_export.pdf');
		}
		
		// file location
		$fullPath = $pdfPath.DS.'loan_export.pdf';
		
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header('Content-disposition: attachment; filename='.basename($fullPath));
		header("Content-Type: application/pdf");
		header("Content-Transfer-Encoding: binary");
		header('Content-Length: '. filesize($fullPath));
		readfile($fullPath);
		exit;

	} 

	catch (Zend_Pdf_Exception $e) {
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
		$this->_forward('select');
	}
	public function newcustomerAction()
	{
		
		$loanId     = $this->getRequest()->getParam('id');
		
		$customerModel  = Mage::getModel('customer/customer')->load($loanId);
       
		if ($customerModel->getEntityId()|| $loanId == 0) {
		$this->loadLayout();
			$this->_setActiveMenu('loan/manage_loan');
		   
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
		   
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		   
			$this->_addContent($this->getLayout()->createBlock('loan/adminhtml_customer_edit'))
				 ->_addLeft($this->getLayout()->createBlock('loan/adminhtml_customer_edit_tabs'));
			   
			$this->renderLayout();
		} else {
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
				
				$firstname=$postData['firstname'];
				$lastname=$postData['lastname'];
				$emailid=$postData['email'];
				$status=$postData['status'];
				$loan_description=$postData['loan_description'];
				$loan_from_date=$postData['loan_from_date'];
				$loan_to_date=$postData['loan_to_date'];
				$deposit_amount=$postData['deposit_amount'];
				
				$collection=Mage::getModel('catalog/product')->load($productid);
		        $productname=$collection->getData('name');
				$loanId = $this->getRequest()->getParam('id');
				
				$loanModel = Mage::getModel('loan/loan');
				
				$loanModel->setId($loanId)
					->setLoanDescription($postData['loan_description'])
					->setLoanFromDate($postData['loan_from_date'])
					->setLoanToDate($postData['loan_to_date'])
					->setDepositAmount($postData['deposit_amount'])
					->setStatus($postData['status'])
					->save();
				$loan = Mage::getModel('loan/loan')->load($loanId);
		        $getCustomerId=$loan->getEntityId();
		
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
					catch (Exception $ex)
					{
						//Zend_Debug::dump($ex->getMessage());
					}
					$loanProducts = Mage::getModel('loan/product')->getCollection()
								->addFieldToFilter("loan_id",$loanId);
					$selectedProducts = $loanProducts->getData(); 
					foreach($selectedProducts as $product)
					{
						$productId = $product['product_id'];
						$collection=Mage::getModel('catalog/product')->load($productId);
						$Names[]=$collection->getData('name');
					}
				
				   $productname = implode("<br>",$Names);
				   
				if($status==2)
				{
				    $this->status_email($loan_description,$loan_from_date,$loan_to_date,$firstname,$lastname,$emailid,$deposit_amount,$productname);
				}				
			   	Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Loan was successfully saved'));
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
	
		if ( $this->getRequest()->getPost() ) {
			try {
			    $postData = $this->getRequest()->getPost();
				
				$firstname=$postData['firstname'];
				$lastname=$postData['lastname'];
				$emailid=$postData['email'];
				$productid=$postData['product_name'];
				$deposit_amount=$postData['deposit_amount'];
				$loan_description=$postData['loan_description'];
			    $loan_from_date=$postData['loan_from_date'];
				$loan_to_date=$postData['loan_to_date'];
				
				$productdetails=Mage::getModel('catalog/product')->load($productid);
				$productname=$productdetails->getData('name');
				
				if($postData['entity_id'])
				{   				
				    $loanId = $this->getRequest()->getParam('id');
					
					$loanModel = Mage::getModel('loan/loan');
					$loanModel->setLoanDescription($postData['loan_description'])
						->setLoanFromDate($postData['loan_from_date'])
						->setLoanToDate($postData['loan_to_date'])
						->setStatus($postData['status'])
						->setEntityId($postData['entity_id'])
						->setDepositAmount($postData['deposit_amount'])
						->save();
					
					$getCustomerId = $loanModel->getData('entity_id');
					$getCustomer = Mage::getModel('customer/customer')->load($getCustomerId);

					$getCustomer->setPrefix($postData['prefix'])
							->setFirstname($postData['firstname'])
							->setMiddlename($postData['middle'])
							->setLastname($postData['lastname'])
							->setSuffix($postData['suffix'])
							->save();
							
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
					catch (Exception $ex)
					{
						//Zend_Debug::dump($ex->getMessage());
					}
					
					$loanId = $loanModel->getId();
					
					$selectedProducts = Mage::getSingleton('adminhtml/session')->getProductDetails();
					foreach($selectedProducts as $product)
					 {
						$loanProducts = Mage::getModel('loan/product');
						$productId = $product['id'];
						$loanProducts->setLoanId($loanId)
							->setProductId($productId)
							->save(); 
							
						$productId = $product['id'];
						$collection=Mage::getModel('catalog/product')->load($productId);
						$Names[]=$collection->getData('name');
						
					 }		  
	                 $productname = implode("<br>",$Names);	
					
					   
				}
				else
				{
					$customerModel = Mage::getModel('customer/customer');
					$password=$customerModel->generatePassword();
								
					$customerModel->setId($this->getRequest()->getParam('id'));
				
					$customerModel->setFirstname($postData['firstname'])
					                    ->setWebsiteId(1)
										->setLastname($postData['lastname'])
										->setEmail($postData['email'])	
										->setPassword($password)
										->save();							
										
					$getCustomerId=$customerModel->getEntityId();
					
					$loanModel = Mage::getModel('loan/loan');
					
					$loanModel->setLoanDescription($postData['loan_description'])
							  ->setLoanFromDate($postData['loan_from_date'])
							  ->setLoanToDate($postData['loan_to_date'])
							  ->setStatus($postData['status'])
							  ->setDepositAmount($postData['deposit_amount'])
							  ->setEntityId($getCustomerId)
							  ->save();
							  
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
					catch (Exception $ex)
					{
						//Zend_Debug::dump($ex->getMessage());
					}
					
					$loanId = $loanModel->getId();
					
					$selectedProducts = Mage::getSingleton('adminhtml/session')->getProductDetails();
					foreach($selectedProducts as $product)
					 {
						$loanProducts = Mage::getModel('loan/product');
						$productId = $product['id'];
						$loanProducts->setLoanId($loanId)
							->setProductId($productId)
							->save(); 
													
						$productId = $product['id'];
						$collection=Mage::getModel('catalog/product')->load($productId);
						$Names[]=$collection->getData('name');
						
					 }		  
					$productname = implode(",",$Names);	
					
					$firstname=$postData['firstname'];
					$lastname=$postData['lastname'];
					$emailid=$postData['email'];
					$this->newcustomeremail($firstname,$lastname,$emailid,$password);
				}
				$this->newloanemail($loan_description,$loan_from_date,$loan_to_date,$firstname,$lastname,$emailid,$deposit_amount,$productname);
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Loan was successfully saved'));
				
				Mage::getSingleton('adminhtml/session')->setloanData(false);
 				$this->_redirect('*/*/');
				return;
			} 
			catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setloanData($this->getRequest()->getPost());
				$this->_redirect('*/*/', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function massAddProductAction()
	{
		$CustomerId = $this->getRequest()->getParam('id');
		$addedproduct=0;
		if($CustomerId)
		{
			echo Mage::getSingleton('adminhtml/session')->setCustomerId($CustomerId);
		}
		$productIds = $this->getRequest()->getParam('entity_id');      // $this->getMassactionBlock()->setFormFieldName('tax_id'); from Mage_Adminhtml_Block_Tax_Rate_Grid
		$count=count($productIds);
		if(!is_array($productIds)) 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('loan')->__('Please select product(s).'));
		} 
		else 
		{
			try {
			$selectedProducts = array();
			$productModel = Mage::getModel('catalog/product');
			foreach ($productIds as $productId) 
			{
				$ids = Mage::getSingleton('adminhtml/session')->getProductDetails();
				if($ids)
				{
					$selectedProducts = Mage::getSingleton('adminhtml/session')->getProductDetails();
					$product = array();
					$product['id'] = $productId;
					$product['name'] = $this->getProductName($productId);
					
					if(in_array($product, $selectedProducts))
					{
					   ++$addedproduct;
					}
					else
					{
						$selectedProducts[] = $product;
						Mage::getSingleton('adminhtml/session')->setProductDetails($selectedProducts);
					}
				}
				else
				{
					$product = array();
					$product['id'] = $productId;
					$product['name'] = $this->getProductName($productId);
					
					$selectedProducts[] = $product;
					
					Mage::getSingleton('adminhtml/session')->setProductDetails($selectedProducts);
				}
			
		}
		if($addedproduct)
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('%d Product(s) has been added already',$addedproduct));
		}
		$totalproduct=count($productIds);
		$total=$totalproduct-$addedproduct;
		Mage::getSingleton('adminhtml/session')->addSuccess(
		Mage::helper('loan')->__('%d Product(s) added.', $total
		)
		);
		} catch (Exception $e) 
		{
		
			   Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		}
		$this->_redirect('*/*/select');
	
	}
	public function deleteAction()
	{
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$loanModel = Mage::getModel('loan/loan');
			   
				$loanModel->setId($this->getRequest()->getParam('id'))
					->delete();
				   
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('loan_id')));
			}
		}
		$this->_redirect('*/*/');
	}
	public function massDeleteAction()
	{
		$loanIds = $this->getRequest()->getParam('id');
		
			if ($this->getRequest()->getParam('id')>0) {
			try {
				 foreach ($loanIds as $loanId) { 
						$loanModel = Mage::getModel('loan/loan')->load($loanId);	
						$loanModel->delete();
					}
				   
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('loan was successfully deleted'));
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
	 public function addproductAction()
	{
	
		$CustomerId = $this->getRequest()->getParam('customer');
		if($CustomerId)
		{
			Mage::getSingleton('adminhtml/session')->setCustomerId($CustomerId);
		}
					
	    $id = $this->getRequest()->getParam('id');
		$ids = Mage::getSingleton('adminhtml/session')->getProductDetails();
		$selectedProducts = array();
		
		if($ids)
		{
			$selectedProducts = Mage::getSingleton('adminhtml/session')->getProductDetails();

			$product = array();
			$product['id'] = $id;
			$product['name'] = $this->getProductName($id);
			
			if(in_array($product, $selectedProducts)){
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Product has been added already'));
			}
			else{
				
				$selectedProducts[] = $product;
				Mage::getSingleton('adminhtml/session')->setProductDetails($selectedProducts);
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Product has been added successfully'));
				
			}

		}
		else
		{
			$product = array();
			$product['id'] = $id;
			$product['name'] = $this->getProductName($id);
			
			$selectedProducts[] = $product;
			
			Mage::getSingleton('adminhtml/session')->setProductDetails($selectedProducts);
			Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Product has been added successfully'));	  
		}
		
		
		$this->_redirect('*/*/select');
	}
	
	public function getProductName($id)
	{
		$collection = Mage::getModel('catalog/product')->load($id);
		$productname = $collection->getData('name');
		return $productname;
	}
	
	
	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			   $this->getLayout()->createBlock('loan/adminhtml_loan_grid')->toHtml()
		);
	}
	public function customergridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			   $this->getLayout()->createBlock('loan/adminhtml_customer_grid')->toHtml()
		);
	}
	public function productgridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			   $this->getLayout()->createBlock('loan/adminhtml_product_grid')->toHtml()
		);
	}
	public function selectAction()
	{
		$CustomerId = $this->getRequest()->getParam('id');
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('loan/adminhtml_product'));
		$this->renderLayout();
	}
	public function customerAction()
	{
		$this->loadLayout()->_setActiveMenu('loan/manage_loan');
		$this->_addContent($this->getLayout()->createBlock('loan/adminhtml_customer'));
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
	public function newloanemail($loan_description,$loan_from_date,$loan_to_date,$firstname,$lastname,$emailid,$deposit_amount,$productname)
	{
		 
		$emailTemplate = Mage::getModel('core/email_template')->loadDefault('loan_confirm_email_template');

		 //Getting the Store E-Mail Sender Name.
		 $senderName = Mage::getStoreConfig('trans_email/ident_general/name');

		 //Getting the Store General E-Mail.
		 $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');

		 //Variables for Twitter Confirmation Mail.
		 $emailTemplateVariables = array();
		 $emailTemplateVariables['name'] = $firstname.' '.$lastname;
		 $emailTemplateVariables['email'] = $emailid;
		 $emailTemplateVariables['productname']=$productname;
		 $emailTemplateVariables['loan_description'] = $loan_description;
		 $emailTemplateVariables['loan_from_date'] = $loan_from_date;
		 $emailTemplateVariables['loan_to_date'] = $loan_to_date;
		 $emailTemplateVariables['deposit_amount']=$deposit_amount;

		 //Appending the Custom Variables to Template.
		 $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);

		 //Sending E-Mail to Customers.
		 $mail = Mage::getModel('core/email')
		 ->setToName($firstname)
		 ->setToEmail($emailid)
		 ->setBody($processedTemplate)
		 ->setSubject('New Loan Item(s)')
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
	     public function status_email($loan_description,$loan_from_date,$loan_to_date,$firstname,$lastname,$emailid,$deposit_amount,$productname)
	{
		 
		$emailTemplate = Mage::getModel('core/email_template')->loadDefault('status_loan_email_template');

		 //Getting the Store E-Mail Sender Name.
		 $senderName = Mage::getStoreConfig('trans_email/ident_general/name');

		 //Getting the Store General E-Mail.
		 $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');

		 //Variables for Twitter Confirmation Mail.
		 $emailTemplateVariables = array();
		 $emailTemplateVariables['name'] = $firstname.' '.$lastname;
		 $emailTemplateVariables['email'] = $emailid;
		 $emailTemplateVariables['productname']=$productname;
		 $emailTemplateVariables['loan_description'] = $loan_description;
		 $emailTemplateVariables['loan_from_date'] = $loan_from_date;
		 $emailTemplateVariables['loan_to_date'] = $loan_to_date;
		 $emailTemplateVariables['deposit_amount']=$deposit_amount;

		 //Appending the Custom Variables to Template.
		 $processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);

		 //Sending E-Mail to Customers.
		 $mail = Mage::getModel('core/email')
		 ->setToName($firstname)
		 ->setToEmail($emailid)
		 ->setBody($processedTemplate)
		 ->setSubject('Your Loan agreement')
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
			$state = "<input type='text' class='required-entry input-text required-entry validate-alpha validation-passed' value='' name='state' id='state'>";
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

