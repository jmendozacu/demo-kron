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
class Velanapps_Ezlogin_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	Function for getting Facebook OAuth file url.
	Input  : Being called, no specific input given.
	Output : Returns Facebook OAuth file url.
	*/
	public function facebookUrl()
	{
		return Mage::getBaseDir("media") . DS . "ezlogin" . DS. "facebook" . DS;
	}
	
	/**
	Function for getting Facebook Application Id.
	Input  : Being called, no specific input given.
	Output : Returns Facebook Application Id.
	*/
	public function facebookAppId()
	{
		return Mage::getStoreConfig('social/facebook/fbappid');
	}
	
	/**
	Function for getting Facebook Application Secret key.
	Input  : Being called, no specific input given.
	Output : Returns Facebook Application Secret key.
	*/
	public function facebookAppSecret()
	{
		return Mage::getStoreConfig('social/facebook/fbappsec');
	}
	
	/**
	Function for all social accounts call back url.
	Input  : Being called, no specific input given.
	Output : Returns Magento call back action url.
	*/
	public function callBackUrl()
	{
		return Mage::getUrl('ezlogin/social/callBack');
	}
	
	/**
	Function for getting Twitter OAuth file Url.
	Input  : Being called, no specific input given.
	Output : Return Twitter Oauth file url.
	*/
	public function twitterUrl()
	{
		return Mage::getBaseDir("media") . DS . "ezlogin" . DS . "twitter" . DS;
	}
	
	/**
	Function for getting Twitter Application Consumer Key.
	Input  : Being called, no specific input given.
	Output : Return Twitter Application Consumer key.
	*/
	public function twitterAppKey()
	{	
		return Mage::getStoreConfig('social/twitter/twappid');
	}
	
	/**
	Function for getting Twitter Application Consumer Secret key.
	Input  : Being called, no specific input given.
	Output : Returns Twitter Application Consumer Secret key.
	*/
	public function twitterAppSecret()
	{
		return Mage::getStoreConfig('social/twitter/twappsec');
	}
	
	/**
	Function for getting Google OAuth File Url.
	Input  : Being called, no specific input given.
	Output : Returns Google OAuth file url.
	*/
	public function googleUrl()
	{
		return Mage::getBaseDir("media") . DS . "ezlogin" . DS . "google" . DS;
	}
	
	
	/**
	Function for getting Yahoo OAuth file Url.
	Input  : Being called, no specific input given.
	Output : Returns Yahoo OAuth file Url.
	*/
	public function yahooUrl()
	{
		return Mage::getBaseDir("media") . DS . "ezlogin" . DS . "yahoo" . DS;
	}
	
	/**
	Function for getting Yahoo Application Key.
	Input  : Being called, no specific input given.
	Output : Returns Yahoo Application Key.
	*/
	public function yahooAppKey()
	{
		return Mage::getStoreConfig('social/yahoo/yahooappkey');
	}
	
	/**
	Function for getting Yahoo Application Secret.
	Input  : Being called, no specific input given.
	Output : Returns Yahoo Application Secret.
	*/
	public function yahooAppSecret()
	{
		return Mage::getStoreConfig('social/yahoo/yahooappsec');
	}
	
	/**
	Function for getting Yahoo Apllication Id.
	Input  : Being called, no specific input given.
	Output : Returns Yahoo Apllication Id.
	*/
	public function yahooAppId()
	{
		return Mage::getStoreConfig('social/yahoo/yahooappid');
	}
	
	/**
	Function for getting Magento store base url.
	Input  : Being called, no specific input given.
	Output : Returns Magento store base url.
	*/
	public function getStoreUrl()
	{
		return Mage::getBaseUrl();
	}
	
	/**
	Function for getting Linked-In Api Oauth file url.
	Input  : Being called, no specific input given.
	Output : Returns Linked-In in Oauth file url.
	*/
	public function linkedInUrl()
	{
		return Mage::getBaseDir("media") . DS . "ezlogin" . DS . "linkedin" . DS;
	}
	
	/**
	Function for getting linked in Application Key.
	Input  : Being called, no specific input given.
	Output : Returns linked in Application Key.
	*/
	public function linkedInAppKey()
	{
		return Mage::getStoreConfig('social/linkedin/linkappid');
	}
	
	/**
	Function for getting Linked-In Application Secret.
	Input  : Being called, no specific input given.
	Output : Returns Linked-In Application Secret.
	*/
	public function linkedInAppKeySecret()
	{
		return Mage::getStoreConfig('social/linkedin/linkappkey');
	}
	
	/**
	Function for getting EzLogin customer table customer details.
	Input  : Social account user unique id, socail account id from ezlogin account table.  
	Output : Returns EzLogin table customer data's.
	*/
	public function getCustomer($socialUserId, $socialAccountId)
	{
		$ezLoginCustomers = Mage::getModel('ezlogin/ezlogincustomers')->getCollection()
						  ->addFieldToFilter('social_id', $socialUserId)
						  ->addFieldToFilter('account_id', $socialAccountId)
						  ->getData();
		
		return $ezLoginCustomers[0];
	}
	
	/**
	Function for getting Magento session object.
	Input  : Being called, no specific input given.
	Output : Returns Magento core session object.
	*/
	public function getSession()
	{	
		return Mage::getSingleton('core/session');
	}
    
	/**
	Function for getting Facebook login image.
	Input  : Being called, no specific input given.
	Output : Returns Facebook login image.
	*/
	public function facebookImage()
	{
		return Mage::getStoreConfig('settings/image/facebook');
	}
	
	/**
	Function for getting Twitter login image.
	Input  : Being called, no specific input given.
	Output : Returns Twitter login image.
	*/
	public function twitterImage()
	{
		return Mage::getStoreConfig('settings/image/twitter');
	}
	
	/**
	Function for getting Google login image.
	Input  : Being called, no specific input given.
	Output : Returns Google login image.
	*/
	public function googleImage()
	{
		return Mage::getStoreConfig('settings/image/google');
	}
	
	/**
	Function for getting Yahoo login image.
	Input  : Being called, no specific input given.
	Output : Returns Yahoo login image.
	*/
	public function yahooImage()
	{
		return Mage::getStoreConfig('settings/image/yahoo');
	}
	
	/**
	Function for getting Linked-In login image.
	Input  : Being called, no specific input given.
	Output : Returns Linked-In login image.
	*/
	public function linkedInImage()
	{
		return Mage::getStoreConfig('settings/image/linkedin');
	}
	
	/**
	Function for getting social image upload url.
	Input  : Being called, no specific input given.
	Output : Returns social image uploaded url.
	*/
	public function loginImageUrl()
	{	
		return "media/ezlogin/images/";
	}
	
	/**
	Function for getting social account Id.
	Input  : Being called, no specific input given.
	Output : Returns social account Id.
	*/
	public function socialAccountId($socialAccountId)
	{
		$data = Mage::getModel('ezlogin/ezloginaccounts')->getCollection()
				->AddFieldToFilter('account_name', $socialAccountId)
				->getData();
		
		$ezLoginAccountId = $data[0]['account_id'];
	
		return $ezLoginAccountId;
	}
	
	/**
	Function for getting social customers profile image validating.
	Input  : Social Account unique Id, Profile Image Url.
	Output : Returns customer profile image count.
	*/
	public function profileImage($socialAccountId, $profileImage)
	{
		$checkCustomer = Mage::getModel('ezlogin/ezlogincustomers')->getCollection()
				->AddFieldToFilter('social_id', $socialAccountId)
				->AddFieldToFilter('profile_image', $profileImage)
				->getData();
		
		$ezLoginCustomer = count($checkCustomer);
	
		return $ezLoginCustomer;
	}
	
	/**
	Function for updating customer profile image url in ezlogin customer table.
	Input  : Social account unique id, Social Customer Profile Image Url.
	Output : No Output return.
	*/
	public function profileImageUpdate($customerId, $profileImage)
	{
		$updateCustomer = Mage::getModel('ezlogin/ezlogincustomers')->load($customerId)
						  ->setProfileImage($profileImage)
						  ->save();
		
		return;
	}
	
	/**
	Function for getting logged in customer profile image.
	Input  : Magento logged in customer id, Social Account Id.
	Output : Return user profile image.
	*/
	public function getUserProfileImage($customerId, $socialAccountId)
	{
		$customerImage = Mage::getModel('ezlogin/ezlogincustomers')->getCollection()
						 ->AddFieldToFilter('mage_customer_id', $customerId)
						 ->AddFieldToFilter('account_id', $socialAccountId)
						 ->getData();
		
		return $customerImage[0];
	}
	
	/**
	Function for getting social Login Icon Width.
	Input  : Being called, no specific input given.
	Output : Returns Social Login Icon Width.
	*/
	public function loginIconWidth()
	{
		return Mage::getStoreConfig('settings/icon/width');
	}
	
	/**
	Function for getting social Login Icon Height.
	Input  : Being called, no specific input given.
	Output : Returns Social Login Icon Height.
	*/
	public function loginIconHeight()
	{
		return Mage::getStoreConfig('settings/icon/height');
	}
	
	/**
	Function for getting Store Url Without index.php.
	Input  : Being called, no specific input given.
	Output : Returns Store Url Without index.php.
	*/
	public function storeLoginImageUrl()
	{
		$baseUrl = Mage::getUrl('',array('_secure'=>true));
		
		if(strpos($baseUrl, 'index.php') !== false)
		{
			$url = explode('index.php', $baseUrl);
		
			$storeUrl =	$url[0];
		}
		else
		{
			$storeUrl =	$baseUrl;
		}
		
		return $storeUrl;
	}
	
	/**
	Function for Getting login icons sort order.
	Input  : Being called, no specific input given.
	Output : Returns login icons sort order.
	*/
	public function iconSortOrder()
	{
		$iconsSortOrder = array("facebook"=>Mage::getStoreConfig('settings/iconsort/facebook'),
								"twitter"=>Mage::getStoreConfig('settings/iconsort/twitter'), 
								"google"=>Mage::getStoreConfig('settings/iconsort/google'),
								"yahoo"=>Mage::getStoreConfig('settings/iconsort/yahoo'),
								"linkedIn"=>Mage::getStoreConfig('settings/iconsort/linkedin')); 
		
		$compareIcons = $iconsSortOrder;
		
		//Checking unique value for icons.
		$iconsSortUnique = array_unique($iconsSortOrder);
		
		//Getting array count for actuall icons.
		$iconsCountActuall = count($iconsSortOrder);
		
		//Getting array count for sorted icons.	
		$iconsUniqueCount = count($iconsSortUnique);
		
		$sortOrderNew = array();
		
		if($iconsCountActuall==$iconsUniqueCount)
		{
			sort($iconsSortOrder);
			
			foreach($iconsSortOrder as $data => $value) 
			{	
				foreach($compareIcons as $key => $val) 
				{
					if($val == $value) 
					{
						$sortOrderNew[] =  $key;
					}
				}
			}
	
		}
		else
		{
			$sortOrderNew = Array('0' => "facebook",'1' => "twitter",'2' => "google",'3' => "yahoo",'4' => "linkedIn");
		}
		
		
		return $sortOrderNew;
	}
}