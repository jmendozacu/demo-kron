<?php
/**
 * Facebook config model
 *
 * @category    Inchoo
 * @package     Inchoo_Facebook
 * @author      Ivan Weiler <ivan.weiler@gmail.com>
 * @copyright   Inchoo (http://inchoo.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Devinc_Groupdeals_Model_Facebook_Config
{
	const XML_PATH_ENABLED = 'groupdeals/facebook_connect/enabled';
	const XML_PATH_API_KEY = 'groupdeals/facebook_connect/api_key';
	const XML_PATH_SECRET = 'groupdeals/facebook_connect/secret';
	const XML_PATH_LOCALE = 'groupdeals/facebook_connect/locale';
	
    public function isEnabled($storeId=null)
    {
		if( Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $storeId) && 
			$this->getApiKey($storeId) && 
			$this->getSecret($storeId))
		{
        	return true;
        }
        
        return false;
    }
	
    public function getApiKey($storeId=null)
    {
    	return trim(Mage::helper('core')->decrypt(Mage::getStoreConfig(self::XML_PATH_API_KEY, $storeId)));
    }
    
    public function getSecret($storeId=null)
    {
    	return trim(Mage::helper('core')->decrypt(Mage::getStoreConfig(self::XML_PATH_SECRET, $storeId)));
    }
    
    public function getRequiredPermissions()
    {
    	return array('email');
    }
    
    public function getLocale($storeId=null)
    {
    	return Mage::getStoreConfig(self::XML_PATH_LOCALE, $storeId);
    }

}
