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
class Velanapps_Ezlogin_Block_Active extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	Function for getting Facebook account enable/disable status.
	Input  : Being called, no specific input given.
	Output : Returns Facebook Enable/Disable status.
	*/
	public function facebookActive()
	{
		return Mage::getStoreConfig('social/facebook/active');
	}
	
	/**
	Function for getting Twitter account enable/disable status.
	Input  : Being called, no specific input given.
	Output : Returns Twitter enable/disable status.
	*/
	public function twitterActive()
	{
		return Mage::getStoreConfig('social/twitter/active');
	}
	
	/**
	Function for getting Google account enable/disable status.
	Input  : Being called, no specific input given.
	Output : Returns Google enable/disable status.
	*/
	public function googleActive()
	{
		return Mage::getStoreConfig('social/google/active');
	}
	
	/**
	Function for getting Yahoo account enable/disable status.
	Input  : Being called, no specific input given.
	Output : Returns Yahoo enable/disable status.
	*/
	public function yahooActive()
	{
		return Mage::getStoreConfig('social/yahoo/active');
	}
	
	/**
	Function for getting Linked-In account enable/disable status.
	Input  : Being called, no specific input given.
	Output : Returns Linked-In enable/disable status.
	*/
	public function linkedInActive()
	{
		return Mage::getStoreConfig('social/linkedin/active');
	}
	
	/**
	Function for getting Facebook Login Url in Login Page.
    Input  : Being called, no specific input given.
	Output : Returns Facebook Login Url in Login Page.
	*/
	public function facebookLoginUrl()
	{	
		$ezLoginHelperRewrite = Mage::helper("ezlogin/rewrite");
		
		//Re-write Block function loading 
		$ezLoginBlockRewrite = $this->getLayout()->createBlock('ezlogin/rewrite');
			
		$facebookRewriteUrl = $ezLoginBlockRewrite->facebookUrlRewrite();
			
		$checkData = $ezLoginHelperRewrite->ezLoginUrlFilter($facebookRewriteUrl);
			
		if(isset($checkData['id_path']))
		{
			$facebookLoginUrl = $checkData['id_path'];		
		}
		else
		{
			$facebookLoginUrl = 'add-facebook';		
		}
		
		return $facebookLoginUrl;
	}
	
	/**
	Function for getting Twitter Login Url in Login Page.
    Input  : Being called, no specific input given.
	Output : Returns Twitter Login Url in Login Page.
	*/
	public function twitterLoginUrl()
	{	
		$ezLoginHelperRewrite = Mage::helper("ezlogin/rewrite");
		
		//Re-write Block function loading 
		$ezLoginBlockRewrite = $this->getLayout()->createBlock('ezlogin/rewrite');
			
		$twitterRewriteUrl = $ezLoginBlockRewrite->twitterUrlRewrite();
			
		$checkData = $ezLoginHelperRewrite->ezLoginUrlFilter($twitterRewriteUrl);
		
		if(isset($checkData['id_path']))
		{
			$twitterLoginUrl = $checkData['id_path'];		
		}
		else
		{	
			$twitterLoginUrl = 'add-twitter';		
		}
		
		return $twitterLoginUrl;
	}
	
	/**
	Function for getting Google Login Url in Login Page.
    Input  : Being called, no specific input given.
	Output : Returns Google Login Url in Login Page.
	*/
	public function googleLoginUrl()
	{	
		$ezLoginHelperRewrite = Mage::helper("ezlogin/rewrite");
		
		//Re-write Block function loading 
		$ezLoginBlockRewrite = $this->getLayout()->createBlock('ezlogin/rewrite');
		
		$googleRewriteUrl = $ezLoginBlockRewrite->googleUrlRewrite();
			
		$checkData = $ezLoginHelperRewrite->ezLoginUrlFilter($googleRewriteUrl);
			
		if(isset($checkData['id_path']))
		{
			$googleLoginUrl = $checkData['id_path'];		
		}
		else
		{
			$googleLoginUrl = 'add-google';		
		}
		
		return $googleLoginUrl;
	}
	
	/**
	Function for getting Yahoo Login Url in Login Page.
    Input  : Being called, no specific input given.
	Output : Returns Yahoo Login Url in Login Page.
	*/
	public function yahooLoginUrl()
	{	
		$ezLoginHelperRewrite = Mage::helper("ezlogin/rewrite");
		
		//Re-write Block function loading 
		$ezLoginBlockRewrite = $this->getLayout()->createBlock('ezlogin/rewrite');
			
		$yahooRewriteUrl = $ezLoginBlockRewrite->yahooUrlRewrite();
			
		$checkData = $ezLoginHelperRewrite->ezLoginUrlFilter($yahooRewriteUrl);
			
		if(isset($checkData['id_path']))
		{
			$yahooLoginUrl = $checkData['id_path'];		
		}
		else
		{
			$yahooLoginUrl = 'add-yahoo';		
		}
		
		return $yahooLoginUrl;
	}
	
	/**
	Function for getting Linked-In Login Url in Login Page.
    Input  : Being called, no specific input given.
	Output : Returns Linked-In Login Url in Login Page.
	*/
	public function LinkedInLoginUrl()
	{	
		$ezLoginHelperRewrite = Mage::helper("ezlogin/rewrite");
		
		//Re-write Block function loading 
		$ezLoginBlockRewrite = $this->getLayout()->createBlock('ezlogin/rewrite');
			
		$linkedInRewriteUrl = $ezLoginBlockRewrite->linkedInUrlRewrite();
			
		$checkData = $ezLoginHelperRewrite->ezLoginUrlFilter($linkedInRewriteUrl);
			
		if(isset($checkData['id_path']))
		{
			$linkedInLoginUrl = $checkData['id_path'];		
		}
		else
		{
			$linkedInLoginUrl = 'add-linkedin';		
		}
		
		return $linkedInLoginUrl;
	}
	
	/**
	Function for getting Facebook Login Image.
    Input  : Being called, no specific input given.
	Output : Returns Facebook Login Image.
	*/
	public function facebookLoginImageUrl()
	{
		$ezLoginHelperData = Mage::helper("ezlogin/data");
		
		$loginImageUrl = $ezLoginHelperData->loginImageUrl(); 
		
		$customFacebookImage = $ezLoginHelperData->facebookImage();
		
		if($customFacebookImage)
		{
			$facebookLoginImage = $loginImageUrl.$customFacebookImage;
		}
		else
		{
			$facebookLoginImage = $loginImageUrl.'ezlogin_facebook.png';
		}
		
		return $facebookLoginImage;
	}
	
	/**
	Function for getting Twitter Login Image.
    Input  : Being called, no specific input given.
	Output : Returns Twitter Login Image.
	*/
	public function twitterLoginImageUrl()
	{
		$ezLoginHelperData = Mage::helper("ezlogin/data");
		
		$loginImageUrl = $ezLoginHelperData->loginImageUrl(); 
		
		$customTwitterImage = $ezLoginHelperData->twitterImage();
		
		if($customTwitterImage)
		{
			$twitterLoginImage = $loginImageUrl.$customTwitterImage;
		}
		else
		{
			$twitterLoginImage = $loginImageUrl.'ezlogin_twitter.png';
		}
		
		return $twitterLoginImage;
	}
	
	/**
	Function for getting Google Login Image.
    Input  : Being called, no specific input given.
	Output : Returns Google Login Image.
	*/
	public function googleLoginImageUrl()
	{
		$ezLoginHelperData = Mage::helper("ezlogin/data");
		
		$loginImageUrl = $ezLoginHelperData->loginImageUrl(); 
		
		$customGoogleImage = $ezLoginHelperData->googleImage();
		
		if($customGoogleImage)
		{
			$googleLoginImage = $loginImageUrl.$customGoogleImage;
		}
		else
		{
			$googleLoginImage = $loginImageUrl.'ezlogin_google.png';
		}
		
		return $googleLoginImage;
	}
	
	/**
	Function for getting Yahoo Login Image.
    Input  : Being called, no specific input given.
	Output : Returns Yahoo Login Image.
	*/
	public function yahooLoginImageUrl()
	{
		$ezLoginHelperData = Mage::helper("ezlogin/data");
		
		$loginImageUrl = $ezLoginHelperData->loginImageUrl(); 
		
		$customYahooImage = $ezLoginHelperData->yahooImage();
		
		if($customYahooImage)
		{
			$yahooLoginImage = $loginImageUrl.$customYahooImage;
		}
		else
		{
			$yahooLoginImage = $loginImageUrl.'ezlogin_yahoo.png';
		}
		
		return $yahooLoginImage;
	}
	
	/**
	Function for getting Linked-In Login Image.
    Input  : Being called, no specific input given.
	Output : Returns Linked-In Login Image.
	*/
	public function linkedInLoginImageUrl()
	{
		$ezLoginHelperData = Mage::helper("ezlogin/data");
		
		$loginImageUrl = $ezLoginHelperData->loginImageUrl(); 
		
		$customLinkedInImage = $ezLoginHelperData->linkedInImage();
		
		if($customLinkedInImage)
		{
			$linkedInImage = $loginImageUrl.$customLinkedInImage;
		}
		else
		{
			$linkedInImage = $loginImageUrl.'ezlogin_linked-in.png';
		}
		
		return $linkedInImage;
	}
	
}
