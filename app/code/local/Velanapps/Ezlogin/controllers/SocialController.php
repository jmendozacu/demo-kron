<?php
/**
 * Velan Info Services India Pvt Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.velanapps.com/License.txt
 *
  /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 * *************************************** */
/* This package designed for Magento COMMUNITY edition
 * Velan Info Services does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Velan Info Services does not provide extension support in case of
 * incorrect edition usage.
  /***************************************
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 * ****************************************************
 * @category   velanapps
 * @package    EzLogin
 * @author     Velan Team
 * @copyright  Copyright (c) 2012 - 2013 Velan Info Services India Pvt Ltd. (http://www.velanapps.com)
 * @license    http://store.velanapps.com/License.txt
 */
class Velanapps_Ezlogin_SocialController extends Mage_Core_Controller_Front_Action
{
	/**
	Function for All Social Account Authtication.
	Input  : Provider Name(facebook, twitter, etc..)
	Output : Connecting respective socail login page. 
	*/
    public function connectAction()
    {
        $socailAccount = $this->getRequest()->getParam('account');
			
		switch($socailAccount)
		{
			case 'facebook':
				$this->facebook();
			break;
			
			case 'twitter':
				$this->twitter();
			break;
			
			case 'google':
				$this->google();
			break;
				
			case 'yahoo':
				$this->yahoo();
			break;
				
			case 'linkedin':
				$this->linkedIn();
			break;
				
		}
	}
	
	/**
	Function for Facebook Login.
	Input  : Being called, no specific input given.
	Output : User connected and redirected to facebook login page.
	*/
	public function facebook()
	{	
		// fetching block function for facebook login
		$facebookObject = $this->getLayout()->createBlock('ezlogin/data');
		
		try
		{	
			//Loadin Facebook API from Block function
			$facebookData  = $facebookObject->facebookLogin();	
		}
		catch(Exception $error)
		{
			Mage::getSingleton('core/session')->addError($error->getMessage());
			return false;
		}
		
		return $facebookData;
	}
	
	
	/**
	Function for Twitter Login.
	Input  : Being called, no specific input given.
	Output : User connected and redirected to twitter login page.
	*/
	public function twitter()
	{		
		// fetching block function for twitter login
		$twitterObject = $this->getLayout()->createBlock('ezlogin/data');
		
		try
		{	
			//Loading Twitter API from Block function
			$twitterData   = $twitterObject->twitterLogin();	
		}
		catch(Exception $error)
		{
			Mage::getSingleton('core/session')->addError($error->getMessage());
			return false;
		}
		
		return $twitterData;
	}
	
	/**
	Function for Google Login.
	Input  : Being called, no specific input given.
	Output : User connected and redirected to google login page.
	*/
	public function google()
	{	
		// fetching block function for google login
		$googleObject  = $this->getLayout()->createBlock('ezlogin/data');
		
		try
		{	
			//Loading Google API from Block function
			$googleData   = $googleObject->googleLogin();	
		}
		catch(Exception $error)
		{
			Mage::getSingleton('core/session')->addError($error->getMessage());
			return false;
		}
		
		return $googleData;
	} 
	
	
	/**
	Function for Yahoo Login.
	Input  : Being called, no specific input given.
	Output : User connected and redirected to yahoo login page.
	*/
	public function yahoo()
	{		
		// block function for yahoo login
		$yahooObject  = $this->getLayout()->createBlock('ezlogin/data');
		
		try
		{	
			//Loading Yahoo API from Block function
			$yahooData   = $yahooObject->yahooLogin();	
		}
		catch(Exception $error)
		{
			Mage::getSingleton('core/session')->addError($error->getMessage());
			return false;
		}
		
		return $yahooData;
	}
	
	/**
	Function for Linked In Login.
	Input  : Being called, no specific input given.
	Output : User connected and redirected to linked in login page.
	*/
	public function linkedIn()
	{
		// block function for linked in login
		$linkedInObject  = $this->getLayout()->createBlock('ezlogin/data');
		
		try
		{	
			//Loading Yahoo API from Block function
			$linkedInData  	 = $linkedInObject->linkedinLogin();	
		}
		catch(Exception $error)
		{
			Mage::getSingleton('core/session')->addError($error->getMessage());
			return false;
		}
		
		return $linkedInData;
	}
	
	
	/**
	Function for twitter e-mail popup template set.
	Input  : Being called, no specific input.
	Output : Shows user with a popup to enter Twitter email and process.
	*/
	public function twitteremailAction()
	{
		$this->loadLayout();
		$this->renderLayout(); 
	}

	
	/**
	Function for All Socail Account's Call Back Action.
	Input  : Social account user response data.
	Output : Creates Magento user and redirects to user dashboard.
	*/
	public function callBackAction()
	{     
		$ezLoginHelper = Mage::helper("ezlogin/data");
			
		$session = Mage::getSingleton('core/session'); 
		
		$dashboardUrl = Mage::helper('customer')->getDashboardUrl();
		
		//Error Handling for User Reject the Application.
		try
		{
			//Facebook provider
			$facebookError = $this->getRequest()->getParam('error');
			
			//Twitter Provider
			$twitterError  = $this->getRequest()->getParam('denied');
			
			//LinkedIn Provider
			$linkedinError  = $this->getRequest()->getParam('oauth_problem');
			
			if($facebookError || $twitterError || $linkedinError)
			{
				throw new Exception('Dear Customer! Allow this application to complete the login process', 0, $error);
			}
		}
		catch(Exception $error)
		{
			$session->addError($error->getMessage());
			echo('<script>window.opener.location="'.$dashboardUrl.'";close();</script>');
			return;
		}
		
		//Facebook provider
		$facebookProvider = $this->getRequest()->getParam('code');
			
		//Twitter Provider
		$twitterProvider = $this->getRequest()->getParam('oauth_verifier');
			
		//Twitter email get
		$twitterEmail = $this->getRequest()->getParam('mail');
			
		//Google Provider
		$googleProvider = $this->getRequest()->getParams('openid.ns'); 
			
		//Yahoo Provider
		$yahooProvider = $this->getRequest()->getParam('provider');
			
		//LinkedIn Provider
		$linkedInProvider = $session->getLinkedinRequestToken();
		
		//If the callback is from linked-In
		if($linkedInProvider)
		{	
			/**
			linked in and twitter response "oauth_verifier" from response
			Once linkedin is logged in twitter condition is set to 0, to avoid conflict 
			because both twitter and linked in returns same time of response to call back
			*/
			$twitterProvider = 0;
			
			try{	
				//Get linked-in user details
				$profileData = $this->linkedIn(); 
			}
			catch(Exception $error)
			{
				$session->addError($error->getMessage());
				return false;
			}
			
			$email      = "email-address";
			$firstName  = "first-name";
			$lastName   = "last-name";
			$pictureUrl = "picture-url";
			
			$socialUserId    = $profileData->id;
			$socialEmail     = $profileData->$email;
			$socialFirstName = $profileData->$firstName;
			$socialLastName  = $profileData->$lastName;
			$socialAccountImage = $profileData->$pictureUrl;
				
			/**
			No gender information provided in linked in, so setting gender option to '' as 
			gender variable for Magento cannot be null when creating user.
			*/
			$socialGender  = '';
			$socialAccountName = 'LinkedIn';
			$isVerified = '1';
			$socialAccountId = $ezLoginHelper->socialAccountId($socialAccountName);
		}
			
		//If the call back is from Facebook
		if($facebookProvider)
		{	
			try
			{
				//Get facebook user details
				$socialUser = $this->facebook(); 
			} 
			catch(Exception $error)
			{
				$session->addError($error->getMessage());
				return false;
			}
				
			$socialUserId    = $socialUser['id'];
			$socialFirstName = $socialUser['first_name'];
			$socialLastName  = $socialUser['last_name'];
			$socialGender    = $socialUser['gender'];
			$socialEmail     = $socialUser['email'];
			
			$socialAccountName = 'Facebook';
			$isVerified = '1';
			
			//Facebook User Profile Image.
			$socialAccountImage = 'https://graph.facebook.com/'.$socialUserId.'/picture';
			$socialAccountId = $ezLoginHelper->socialAccountId($socialAccountName); 
		}
			
		//If the call back is from Twitter
		if($twitterProvider) 
		{	
			try{
				//Get twitter user details
				$socialUser   = $this->twitter();
			}
			catch(Exception $error)
			{
				$session->addError($error->getMessage());
				return false;
			}
			
			$socialUserId     = $socialUser->id;
			$socialFirstName  = $socialUser->name;
			$socialLastName   = ' ';
			$socialAccountName = 'Twitter';
			
			//Twitter Profile Image Url.
			$socialAccountImage = $socialUser->profile_image_url;
			$socialAccountId = $ezLoginHelper->socialAccountId($socialAccountName);
			
			/**
			No gender information provided in twitter, so setting gender option to '' as 
			gender variable for Magento cannot be null when creating user.
			*/
			$socialGender     = '';
		    $twitterUser = $ezLoginHelper->getCustomer($socialUserId, $socialAccountId);
			$session->setTwitterUserId($socialUser->id);
			$session->setTwitterFirstName($socialUser->name);
			$session->setTwitterProfileImage($socialUser->profile_image_url);
					
			if(!$twitterUser['mage_customer_id'])
			{	
				//Show twitter email popup to user and process
				$redirectUrl = Mage::getUrl('ezlogin/social/twitteremail');
				
				echo('<script>window.opener.location="'.$redirectUrl.'";close();</script>');
				return;
			}
			
			//Unset Session using inbuilt function
			$session->unsTwitterUserId();
			$session->unsTwitterFirstName();
			$session->unsTwitterProfileImage();
		}
			
		if($twitterEmail=='twitter')
		{	
			//Get the twitter E-mail
			$twittEmail = $this->getRequest()->getPost();
					
			$socialEmail  = $twittEmail['twitter_email'];
					
			$socialUserId       = $session->getTwitterUserId();
			$socialFirstName    = $session->getTwitterFirstName();
			$socialAccountImage = $session->getTwitterProfileImage();
			
			$socialLastName   = ' ';
			$socialAccountName = 'Twitter';
			$isVerified = '0';
			$socialAccountId = $ezLoginHelper->socialAccountId($socialAccountName);
				
			/**
			No gender information provided in twitter, so setting gender option to '' as 
			gender variable for Magento cannot be null when creating user.
			*/
			
			$socialGender     = ''; 
			$session->unsTwitterUserId();
			$session->unsTwitterFirstName();
			$session->unsTwitterProfileImage();
		}
			
		//If the call back is from Google
		if(!empty($googleProvider['openid_mode'])) 
		{	
			try
			{
				//Reading Google User Details.
				$socialFirstName  = $googleProvider['openid_ext1_value_firstname'];
				$socialLastName   = $googleProvider['openid_ext1_value_lastname'];
				$socialEmail      = $googleProvider['openid_ext1_value_email'];
			}
			catch(Exception $error)
			{
				$session->addError($error->getMessage());
				return false;
			}	
			
			/**
			No gender information provided in google, so setting gender option to '' as 
			gender variable for Magento cannot be null when creating user.
			*/
			$socialGender     = '';
				
			//Get google user unique id
			$openId = explode("=", $googleProvider['openid_identity']); 
			$socialUserId = $openId[1]; 
			$socialAccountName = 'Google';
			$isVerified = '1';
			$socialAccountImage = 'http://profiles.google.com/s2/photos/profile/me?sz=32&cache_fix='.$socialUserId;
			$socialAccountId = $ezLoginHelper->socialAccountId($socialAccountName);
		}
			
		//If the call back is form yahoo
		if($yahooProvider=='yahoo')
		{
			$helperData = Mage::helper("ezlogin/data");
				
			require $helperData->yahooUrl().'Yahoo.inc';
				
			$yahooSession = YahooSession::requireSession($helperData->yahooAppKey(), $helperData->yahooAppSecret(), $helperData->yahooAppId());
				
			if($yahooSession) 
			{	
				try
				{
					//Read User Details
					$user    = $yahooSession->getSessionedUser();
					$profile = $user->getProfile();
				}
				catch(Exception $error)
				{
					$session->addError($error->getMessage());
					return false;
				}
				
				//Social Account Image Url
				$socialAccountImage = $profile->image->imageUrl;
				$socialUserId    = $profile->guid;
				$socialFirstName = $profile->givenName;
				$socialLastName  = $profile->familyName;
				$yahooEmail = $profile->emails;
				
				//Checking the primay email with yahoo
				foreach($yahooEmail as $email)
				{	
					if($email->primary)
					{
						$socialEmail = $email->handle;
					}
				}
				
				$socialAccountName = 'Yahoo';
				$isVerified = '1';
				$socialAccountId = $ezLoginHelper->socialAccountId($socialAccountName);
				
				//Checking Gender Using Ternary Operator.
				$socialGender = ($profile->gender == 'M') ? 'Male' : 'Female';
				YahooSession::clearSession($sessionStore);
			}		
		}
					
		if($socialAccountId) 
		{  	
			//Get the customer session
			$customerSession = Mage::getSingleton('customer/session');
			
			//Checking ezlogin customer table to checking data's
		    $ezLoginCustomerData = $ezLoginHelper->getCustomer($socialUserId, $socialAccountId);
			
			//Set the logged in customer Social Account
			$session->setSocialAccountId($socialAccountId);
			
		    if($ezLoginCustomerData['mage_customer_id'])
		    {	
				//Checking ezlogin customer table for profile image changes.
				$ezLoginCustomerProfileImage = $ezLoginHelper->profileImage($socialUserId, $socialAccountImage);
		
				//Updating Customer Profile image, once new image is occur from API.
				if(!$ezLoginCustomerProfileImage)
				{
					$ezLoginHelper->profileImageUpdate($ezLoginCustomerData['customer_id'], $socialAccountImage);
				}
				
				if($ezLoginCustomerData['is_verified'])
				{
					//If social account is exist, customer will login with customer id.
					$customerSession->loginById($ezLoginCustomerData['mage_customer_id']);
				}
				else
				{
					$session->addError('Dear Customer! Check your E-Mail and click the activation link to Login');
					echo('<script>window.opener.location="'.$dashboardUrl.'";close();</script>');
					return;
				}
								
		    }
			else
			{
				if($socialEmail)
				{
					//Get the store information
					$storeId   = Mage::app()->getStore()->getStoreId();
					$websiteId = Mage::getModel('core/store')->load(Mage::app()->getStore()->getStoreId())->getWebsiteId();
							 
					//Check the Facebook email with customer table
					$customerData = Mage::getModel('customer/customer')->getCollection()
									->addFieldToFilter('email', $socialEmail)
									->addFieldToFilter('store_id', $storeId)
									->addFieldToFilter('website_id', $websiteId)
									->getData();
					
					$getCustomerData = $customerData[0];
									
					$entityId = $getCustomerData['entity_id'];
				
					if($entityId) 
					{  
						//Insert customer details with ezlogin customer table
						$data = Mage::getModel('ezlogin/ezlogincustomers')
								->setMageCustomerId($entityId)
								->setAccountId($socialAccountId)
								->setSocialId($socialUserId)
								->setProfileImage($socialAccountImage)
								->setIsVerified($isVerified)							
								->save();
									
						if($socialAccountId==2)
						{
							$this->_confirmationEmail($socialEmail);
							
							//Redirecting to Magento twitter customer notification page
							$this->_redirect('ezlogin/social/emailconfirmation'); 
							return;
						}	
						else
						{
							//Customer login by Magento customer entiry id.
							$customerSession->loginById($entityId);
						}
							
					} 
					else 
					{ 	
						//If login customer not present in magento , creating new magento acoount.
						$customer = Mage::getModel('customer/customer')
									->setFirstname($socialFirstName)
									->setLastname($socialLastName)
									->setEmail($socialEmail)
									->setGender(
											Mage::getResourceModel('customer/customer')
											->getAttribute('gender')
											->getSource()
											->getOptionId($socialGender)
									)
									->setIsActive(1)
									->setConfirmation(null)
									->save();
					
						$mageCustomer   = $customer->getData();
						$mageCustomerId = $mageCustomer['entity_id'];
						
						$this->savePassword($mageCustomerId);
						
						$ezLoginData = Mage::getModel('ezlogin/ezlogincustomers')
									   ->setMageCustomerId($mageCustomerId)
									   ->setAccountId($socialAccountId)
									   ->setSocialId($socialUserId)
									   ->setProfileImage($socialAccountImage)
									   ->setIsVerified($isVerified)		
									   ->save();
								 
						if($socialAccountId==2)
						{
							$this->_confirmationEmail($socialEmail);
							
							//Redirecting to Magento Twitter customer notification page
							$this->_redirect('ezlogin/social/emailconfirmation'); 
							return;
						}
						else
						{
							$customerSession->setCustomerAsLoggedIn($customer);
							$customerId = $customerSession->getCustomerId();
						}
					}
				}
				else
				{
					$session->addError('Dear Customer! Allow this application to complete the login process');
				}
			}
			
			if($socialAccountName=='LinkedIn')
			{	
				Mage::getSingleton('core/session')->getMessages(true);
				$this->_redirect('customer/account'); 
				return;
			}
			else
			{
				echo('<script>window.opener.location="'.$dashboardUrl.'";close();</script>');
				return;	
			}	
		}
	}
	
	/**
	Function for Twitter Customer notification Template.
	Input  : Being called, no specific input given.
	Output : Twitter Customer notification Template.
	*/
	public function emailconfirmationAction()
	{
		$this->loadLayout();
		$this->renderLayout(); 
	}
	
	/**
	Function for Saving random passsword and send to customer email.
	Input  : Registered customer entiry id.
	Output : Password will store and Email will send to customer registered email.
	*/
	public function savePassword($mageCustomerId)
	{
		$passwordLength = 7;
					
		$getCustomer = Mage::getModel('customer/customer')->getCollection()
					   ->addAttributeToFilter('entity_id', $mageCustomerId);

		//Set random password for new login customer and send mail to customer.				
		foreach ($getCustomer as $savepass)
		{
			$savepass->setPassword($savepass->generatePassword($passwordLength))->save();
			//$savepass->sendNewAccountEmail();
		}
		
		return;
	}
	
	/**
	Function for Send the Customer E-Mail Confirmation.
	Input  : Magento customer E-Mail Id.
	Output : Send the E-Mail to Customer E-Mail id Confirmation.
	*/
	private function _confirmationEmail($eMail)
	{	
		$customer = Mage::getModel('customer/customer');
       	
        if($eMail) 
		{  
			$customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($eMail);
			
			$getCustomerData = $customer->getData();
			
			$customerEmail     = $getCustomerData['email'];
			$customerFirstName = $getCustomerData['firstname'];
			$customerLastName  = $getCustomerData['lastname'];
			$customerEntityId  = $getCustomerData['entity_id'];
		
			$emailLink = Mage::helper('ezlogin/data')->getStoreUrl().'ezlogin/social/confirmation/?id='.$customerEntityId;
		}
		
		$emailTemplate  = Mage::getModel('core/email_template')->loadDefault('twitter_email_template');
		
		//Getting the Store E-Mail Sender Name.
		$senderName = Mage::getStoreConfig('trans_email/ident_general/name');
		
		//Getting the Store General E-Mail.
		$senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');
		
		//Variables for Twitter Confirmation Mail.
		$emailTemplateVariables = array();
		$emailTemplateVariables['name'] = $customerFirstName.' '.$customerLastName;
		$emailTemplateVariables['email'] = $customerEmail;
		$emailTemplateVariables['confirmurl'] = $emailLink;
		
		//Appending the Custom Variables to Template.	
		$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
		
		//Sending E-Mail to Customers.
		$mail = Mage::getModel('core/email')
				->setToName($senderName)
				->setToEmail($customerEmail)
				->setBody($processedTemplate)
				->setSubject('Account confirmation for '.$customerFirstName.' '.$customerLastName)
				->setFromEmail($senderEmail)
				->setFromName($senderName)
				->setType('html');
		
		try{
			//Confimation E-Mail Send
			$mail->send();
		}
		catch(Exception $error)
		{
			Mage::getSingleton('core/session')->addError($error->getMessage());
			return false;
		}
		return;
	}
	
	/**
	Function for Magento Customer E-Mail Confirmation link.
	Input  : Being called, no specific input given.
	Output : Redirecting to customer Dashboard page after.
	*/
	public function confirmationAction()
	{   
		$ezLoginHelper = Mage::helper("ezlogin/data");
		
		$customerId = Mage::app()->getRequest()->getParam("id");
		
		$customerSession = Mage::getSingleton('customer/session');
		
        if($customerSession->isLoggedIn())
		{
            $this->_redirect('*/*/');
            return;
        }
		
		try{
		
			$socialAccountName = 'Twitter';
		
			$accountId = $ezLoginHelper->socialAccountId($socialAccountName);
			
			//Set the logged in customer Social Account
			Mage::getSingleton('core/session')->setSocialAccountId($accountId);
			
			$sAuthData =  Mage::getModel('ezlogin/ezlogincustomers')->getCollection()
						  ->AddFieldToFilter('mage_customer_id', $customerId)
						  ->AddFieldToFilter('account_id', $accountId);
		
			$filterData = $sAuthData->getData();
		
			$sAuthCustomerId = $filterData[0]['customer_id'];
		
			$sAuthData =  Mage::getModel('ezlogin/ezlogincustomers')->load($sAuthCustomerId)
						  ->setIsVerified('1')
						  ->save();
		
			//Customer Login session login by customer id.
			$customerSession->loginById($customerId);
		}
		catch(Exception $error)
		{
			Mage::getSingleton('core/session')->addError($error->getMessage());
			return false;
		}
		
		//Redirecting to Magento account Dashboard page.
		$this->_redirectUrl(Mage::helper('customer')->getDashboardUrl()); 
		return;
	}
	
}