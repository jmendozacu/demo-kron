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
class Velanapps_Ezlogin_Block_Data extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	Function for Loading Facebook login API.
	Input  : Being called, no specific input given.
	Output : Prompts user with Facebook login window and returns logged in user details to call back action.
	*/
	public function facebookLogin()
	{
		$ezLoginHelper = Mage::helper("ezlogin/data");
			
		//Include facebook OAuth files
		require_once $ezLoginHelper->facebookUrl().'facebook.php'; 
			
		//Get facebook App id , App secret, Callback Url
		$appId 		 = $ezLoginHelper->facebookAppId();
		$appSecret   = $ezLoginHelper->facebookAppSecret();
		$callbackurl = $ezLoginHelper->callBackUrl();
			
		//Get facebook login response code
		$code = $this->getRequest()->getParam('code');
			
		//If code is empty, then user has not logged into Facebook, and we are showing the prompt
		if(empty($code)) 
		{
			$dialogUrl = 'https://www.facebook.com/dialog/oauth?client_id='.$appId.'&redirect_uri='.urlencode($callbackurl).'&scope=email&display=popup';
			
			echo("<script>top.location.href='".$dialogUrl."'</script>");
		}
					
		//Get user access_token
		$tokenUrl = 'https://graph.facebook.com/oauth/access_token?client_id='.$appId.'&redirect_uri='.urlencode($callbackurl).'&client_secret='.$appSecret.'&code='.$code;
	
		//Facebook Access Token
		$accessToken = file_get_contents($tokenUrl);
			
		//Get logged in user details
		$fqlQuery = 'https://graph.facebook.com/me?'.$accessToken;
		$fqlQueryResult = file_get_contents($fqlQuery);
		$me = json_decode($fqlQueryResult, true);
		
		return $me;
	}
	
	/**
	Function for Loading Twitter login API.
	Input  : Being called, no specific input given.
	Output : Prompts user with Twitter login window and returns logged in user details to call back action.
	*/
	public function twitterLogin()
	{
		$ezLoginHelper = Mage::helper("ezlogin/data");
			
		//Helper Session
		$session     =  $ezLoginHelper->getSession();
			
		//Include Twitter OAuth files
		require_once $ezLoginHelper->twitterUrl().'twitteroauth.php'; 
			
		//Get Twitter App id, App secret, Callback Url
		define('CONSUMER_KEY', $ezLoginHelper->twitterAppKey());
		define('CONSUMER_SECRET', $ezLoginHelper->twitterAppSecret());
		define('OAUTH_CALLBACK', $ezLoginHelper->callBackUrl());
		
		$followRequest = $this->getRequest()->getParams('follow'); 
			
		if($followRequest)
		{
			$session->setTwitterFollow($followRequest);
		}
		
		//Connecting Twitter
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
			
		if($session->getTwitterOauthToken() && ($session->getTwitterOauthSecretToken())) 
		{   
			$sessionOauthToken       = $session->getTwitterOauthToken();
			$sessionOauthTokenSecret = $session->getTwitterOauthSecretToken();
				
			$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $sessionOauthToken, $sessionOauthTokenSecret);
				
			//Get Twitter OAuth verifier
			$oauthVerifierRequest = $this->getRequest()->getParams('oauth_verifier'); 
				
			//Get Twitter access token
			$accessToken = $connection->getAccessToken($oauthVerifierRequest['oauth_verifier']);
				
			$session->setTwitterAccessTokenVerifier($accessToken);

			$session->unsTwitterOauthToken();
			$session->unsTwitterOauthSecretToken();
				 
			if (200 == $connection->http_code) 
			{							
				$statusVerify = 'verified'; 
				
				$session->setTwitterOauthStatus($statusVerify);
					
				$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $accessToken['oauth_token'], $accessToken['oauth_token_secret']);
				
				$userDetails = $connection->get('account/verify_credentials');
				
				//Returns the logged in user details
				return $userDetails;
			} 
			else
			{
				//Unset session
				$session->unsTwitterAccessTokenVerifier();
				$session->unsTwitterFollow();
			}
				
		}
		else
		{	
			//Else Twitter session is not logged in, page will redirect to twitter API.
			$requestToken = $connection->getRequestToken(OAUTH_CALLBACK);
		
			$token = $requestToken['oauth_token'];
		   
			$session->setTwitterOauthToken($requestToken['oauth_token']);
			
			$session->setTwitterOauthSecretToken($requestToken['oauth_token_secret']);
			
			switch ($connection->http_code) 
			{
				case 200:
					$url = $connection->getAuthorizeURL($token);
					
					//Code for Twitter Force Logout.
					$loginUrl =	$url.'&force_login=true';
					
					echo("<script>top.location.href='".$loginUrl."'</script>");
				break;
			}
		}
	}
	
	/**
	Function for Loading Google login API.
	Input  : Being called, no specific input given.
	Output : Prompts user with Google login window and returns logged in user details to call back action.
	*/
	public function googleLogin()
	{
		$ezLoginHelper = Mage::helper("ezlogin/data");
		
		//Google api include Url
		require_once $ezLoginHelper->googleUrl().'openid.php'; 
		
		//Api Call beck Url
		define('CALLBACK_URL', $ezLoginHelper->callBackUrl());
		
		//Getting user details using google api open id
		$openId = new LightOpenID;
		
		$openId->identity = 'https://www.google.com/accounts/o8/id';
		$openId->returnUrl = CALLBACK_URL;
		$endPoint = $openId->discover('https://www.google.com/accounts/o8/id');
		$fields = '?openid.ns=' . urlencode('http://specs.openid.net/auth/2.0') .
		            '&openid.return_to=' . urlencode($openId->returnUrl) .
		            '&openid.claimed_id=' . urlencode('http://specs.openid.net/auth/2.0/identifier_select') .
		            '&openid.identity=' . urlencode('http://specs.openid.net/auth/2.0/identifier_select') .
		            '&openid.mode=' . urlencode('checkid_setup') .
		            '&openid.ns.ax=' . urlencode('http://openid.net/srv/ax/1.0') .
		            '&openid.ax.mode=' . urlencode('fetch_request') .
					'&openid.ui.mode=' . urlencode('popup') .
					'&openid.ui.icon=' . urlencode('true') .
		            '&openid.ax.required=' . urlencode('email,firstname,lastname') .
		            '&openid.ax.type.firstname=' . urlencode('http://axschema.org/namePerson/first') .
		            '&openid.ax.type.lastname=' . urlencode('http://axschema.org/namePerson/last') .
		            '&openid.ax.type.email=' . urlencode('http://axschema.org/contact/email');
		
		$url = $endPoint . $fields;
	
		//Redirecting page to Login window
		echo("<script>top.location.href='".$url."'</script>");
		return;
	}
	
	/**
	Function for Loading Yahoo login API.
	Input  : Being called, no specific input given.
	Output : Prompts user with Yahoo login window and returns logged in user details to call back action.
	*/
	public function yahooLogin()
	{
		$ezLoginHelper = Mage::helper("ezlogin/data");
			
		$callBack = $ezLoginHelper->callBackUrl();
			
		//Including yahoo Api 
		require $ezLoginHelper->yahooUrl().'Yahoo.inc';

		//Initializing yahoo Consumer key, Consumer Secret, Domain name, App id.
		define('OAUTH_CONSUMER_KEY', $ezLoginHelper->yahooAppKey());
		define('OAUTH_CONSUMER_SECRET', $ezLoginHelper->yahooAppSecret());
		define('OAUTH_DOMAIN', $ezLoginHelper->getStoreUrl());
		define('OAUTH_APP_ID', $ezLoginHelper->yahooAppId());
			
		//Yahoo customer session checking 
		$hasSession = YahooSession::hasSession(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_APP_ID);
			
		//Read the parameters from yahoo call back response
		$yahooToken = $this->getRequest()->getParam('oauth_token');
		$yahooVerifier = $this->getRequest()->getParam('oauth_verifier');
			
		//If session is false page will redirect to Api login screen
		if($hasSession == FALSE) 
		{	
			//Getting Yahoo Current Page.
			$callBack = $ezLoginHelper->getStoreUrl().'ezlogin/social/connect/account/yahoo';
				
			$sessionStore = new NativeSessionStore();
				
			$authUrl = YahooSession::createAuthorizationUrl(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, $callBack, $sessionStore);
			
			//Url for Yahoo Force Logout.
			$loginUrl = 'https://login.yahoo.com/config/login?logout=1&.direct=1&.done='.$authUrl;
			
			echo("<script>top.location.href='".$loginUrl."'</script>");
			return;
		}
		else 
		{	
			//If customer is in session, page redirecting to callBack Action
			$callBack     = $ezLoginHelper->callBackUrl();
				
			$callBackUrl  = $callBack.'?provider=yahoo';
				
			echo("<script>top.location.href='".$callBackUrl."'</script>");
			return;
		}
	}

	/**
	Function for Loading Linked-In login API.
	Input  : Being called, no specific input given.
	Output : Prompts user with Linked-In login window and returns logged in user details to call back action.
	*/
	public function linkedinLogin()
	{
		$ezLoginHelper = Mage::helper("ezlogin/data");
		
		$session = Mage::getSingleton('core/session');
		
		//Linked-In request token
		$linkedInSession = $session->getLinkedinRequestToken();
			
		//Initializing linkedin app id, app secret, call backurl.
		$appId       = $ezLoginHelper->linkedInAppKey();
		$appSecret   = $ezLoginHelper->linkedInAppKeySecret();
		$callBackUrl = $ezLoginHelper->callBackUrl(); 					
			
		//Include linkedin OAuth files
		require_once $ezLoginHelper->linkedInUrl().'linkedin.php'; 
			
		if(empty($linkedInSession)) 
		{
			//If linkedin session is empty , page will redirect to linkedin Api
			$linkedinData = new LinkedIn($appId, $appSecret, $callBackUrl);
				
			$linkedinData->getRequestToken(); 
				
			$session->setLinkedinRequestToken(serialize($linkedinData->request_token));
		  
			$url = $linkedinData->generateAuthorizeUrl();
			
			echo("<script>top.location.href='".$url."'</script>");
			return;
		}
		else
		{	
			//If linkedin session is in
 			$callBackData  = new LinkedIn($appId, $appSecret, $callBackUrl);
							
			//Checking linkedin OAuth token variable, OAuth verifier variable.
			$oauthToken    = $this->getRequest()->getParam('oauth_token');
			$oauthVerifier = $this->getRequest()->getParam('oauth_verifier');
				
			if(isset($oauthVerifier))
			{ 	
				//If Oauth verifier is in, fetching user details
				$oauthVerifierRequest = $oauthVerifier; 
					
				$sessionOauthVerifier = $session->setOauthVerifier($oauthVerifierRequest);
				$sessionRequestToken  = $session->getLinkedinRequestToken();
					
				//Unset session
				$callBackData->request_token    =   unserialize($sessionRequestToken);
				$callBackData->oauth_verifier   =   $sessionOauthVerifier;
				$callBackData->getAccessToken($oauthVerifierRequest);
				$sessionOauthAccessToken = $session->setOauthAccessToken(serialize($callBackData->access_token));
					
				//Redirecting to callback Action
				echo('<script>window.opener.location="'.$callBackUrl.'";close();</script>');
				return;
			}
			else
			{	
				//Getting linkedin user details 
				$sessionRequestToken     = $session->getLinkedinRequestToken();
				$sessionOauthVerifier    = $session->getOauthVerifier();
				$sessionOauthAccessToken = $session->getOauthAccessToken();
				$callBackData->request_token    =   unserialize($sessionRequestToken);
				$callBackData->oauth_verifier   =   $sessionOauthVerifier;
				$callBackData->access_token     =   unserialize($sessionOauthAccessToken);
			}
				
			//Unset Session
			$session->unsLinkedinRequestToken();
			$session->unsOauthVerifier();
			$session->unsOauthAccessToken();
				
			//Api Input parameters
			$xmlResponse = $callBackData->getProfile("~:(id,first-name,last-name,email-address,date-of-birth,picture-url)");
				
			//Get details from xml
			$profileData = simplexml_load_string($xmlResponse);
			
			//Return user profile data
			return $profileData;
		}
	}
	
}