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
class Velanapps_Ezlogin_Block_Rewrite extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	Function for Facebook url re-write.
	Input  : Being called, no specific input given.
	Output : Returns Facebook url re-write method.
	*/
	public function facebookUrlRewrite()
	{
		return Mage::getStoreConfig('settings/url/facebook');
	}
	
	/**
	Function for Twitter url re-write.
	Input  : Being called, no specific input given.
	Output : Returns Twitter url re-write method.
	*/
	public function twitterUrlRewrite()
	{
		return Mage::getStoreConfig('settings/url/twitter');
	}
	
	/**
	Function for Google url re-write.
	Input  : Being called, no specific input given.
	Output : Returns Google url re-write method.
	*/
	public function googleUrlRewrite()
	{
		return Mage::getStoreConfig('settings/url/google');
	}
	
	/**
	Function for Yahoo url re-write.
	Input  : Being called, no specific input given.
	Output : Returns Yahoo url re-write method.
	*/
	public function yahooUrlRewrite()
	{
		return Mage::getStoreConfig('settings/url/yahoo');
	}
	
	/**
	Function for Linked-In url re-write.
	Input  : Being called, no specific input given.
	Output : Returns Linked-In url re-write method.
	*/
	public function linkedInUrlRewrite()
	{
		return Mage::getStoreConfig('settings/url/linkedin');
	}
	
	/**
	Function for Magento core url re-write.
	Input  : Being called, no specific input given.
	Output : Writes custom url configured in backend to Core url re-write, no return output.
	*/
	public function ezloginCoreUrlRewrite()
	{
		$ezLoginHelperRewrite = Mage::helper("ezlogin/rewrite");
		
		//Custom Facebook Url Re-write
		if($this->facebookUrlRewrite())
		{
			$facebookCustomUrl = $this->facebookUrlRewrite(); 
			
			$originalUrl 	  = 'ezlogin/social/connect/account/facebook';	
			
			$filteredUrl = 	$ezLoginHelperRewrite->ezLoginUrlFilter($facebookCustomUrl);
			
			//Checks if the newly configured custom url is already present, if not insert core url re-write table
			if(empty($filteredUrl))
			{		
				$ezLoginHelperRewrite->ezLoginUrlRewrite($facebookCustomUrl, $originalUrl);
			}
		}	
		
		//Custom Twitter Url Re-write 
		if($this->twitterUrlRewrite())
		{
			$twitterCustomUrl = $this->twitterUrlRewrite(); 
			
			$originalUrl 	  = 'ezlogin/social/connect/account/twitter';	
			
			$filteredUrl = 	$ezLoginHelperRewrite->ezLoginUrlFilter($twitterCustomUrl);
			
			//Checks if the newly configured custom url is already present, if not insert core url re-write table
			if(empty($filteredUrl))
			{	
				$ezLoginHelperRewrite->ezLoginUrlRewrite($twitterCustomUrl, $originalUrl);
			}
		}
		
		//Custom Google Url Re-write 
		if($this->googleUrlRewrite())
		{
			$googleCustomUrl = $this->googleUrlRewrite();
			
			$originalUrl 	 = 'ezlogin/social/connect/account/google';	
			
			$filteredUrl = 	$ezLoginHelperRewrite->ezLoginUrlFilter($googleCustomUrl);
			
			//Checks if the newly configured custom url is already present, if not insert core url re-write table
			if(empty($filteredUrl))
			{	
				$ezLoginHelperRewrite->ezLoginUrlRewrite($googleCustomUrl, $originalUrl);
			}
		}
		
		//Custom Yahoo Url Re-write 
		if($this->yahooUrlRewrite())
		{
			$yahooCustomUrl = $this->yahooUrlRewrite();
			
			$originalUrl 	= 'ezlogin/social/connect/account/yahoo';	
			
			$filteredUrl = 	$ezLoginHelperRewrite->ezLoginUrlFilter($yahooCustomUrl);
			
			//Checks if the newly configured custom url is already present, if not insert core url re-write table
			if(empty($filteredUrl))
			{	
				$ezLoginHelperRewrite->ezLoginUrlRewrite($yahooCustomUrl, $originalUrl);
			}
		}
		
		//Custom Linked-In Url Re-write 
		if($this->linkedInUrlRewrite())
		{
			$linkedinCustomUrl = $this->linkedInUrlRewrite();
			
			$originalUrl 	   = 'ezlogin/social/connect/account/linkedin';	
			
			$filteredUrl = 	$ezLoginHelperRewrite->ezLoginUrlFilter($linkedinCustomUrl);
			
			//Checks if the newly configured custom url is already present, if not insert core url re-write table
			if(empty($filteredUrl))
			{	
				$ezLoginHelperRewrite->ezLoginUrlRewrite($linkedinCustomUrl, $originalUrl);
			}
		}
		return;
	}
	
}
