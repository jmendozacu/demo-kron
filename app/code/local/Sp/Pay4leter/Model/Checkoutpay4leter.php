<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_pay4later
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */



class Sp_Pay4leter_Model_Checkoutpay4leter extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_pay4later_Model_Info
     */
    protected $_code  = 'pay4leter';
  	protected $_formBlockType = 'pay4leter/form_pay4leter';
  //	protected $_infoBlockType = 'pay4leter/info_checkoutinfo';
 	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = false;
	protected $_canUseForMultishipping  = false;

	
   	public function assignData($data)
	{

	    if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $info->setApiKey($data->getApiKey());
	     
	    return $this;
	}
 	
 	public function getOrderPlaceRedirectUrl()
	{
	//when you click on place order you will be redirected on this url, if you don't want this action remove this method
		return Mage::getUrl('pay4leter/pay4leter/redirect', array('_secure' => true));
	}
	public function getStandardCheckoutFormFields($option = ''){
		//$data1 = new Varien_Object($data);
		$data	= $this->getQuoteData($option);
		
		return $data;
		
	}
	public function getPay4laterUrl()
    {
		$_payHelper = Mage::helper('pay4leter');
		$_payHelper->getPay4leterTypeName();
		 $url=$_payHelper->redirectUrl;
       
         return $url;
    }

    public function getQuoteData($option = '')
    {					
		if ($option == 'redirect') 
		{

    		$orderIncrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
    		$quote = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		} 
		else 
		{
			$quote = $this->getQuote();
		}
		$data=array();
	
		if ($quote)
		{
			
			
			
			$MerchantId 		= Mage::getStoreConfig('payment/pay4later/merchantid');
			$OrderId 			= Mage::getSingleton('checkout/session')->getLastRealOrderId();
			
			$Amount  			= Mage::app()->getStore()->roundPrice($quote->getGrandTotal());
			$encryptionkey 		= Mage::getStoreConfig('payment/pay4later/encryptionkey');
			$Url 				= $this->getPay4laterUrl();
			$cancel_url			= 'http://localhost/magento';
			
			$session = Mage::getSingleton('checkout/session');
			$pay4laterData = $session->getPay4laterData(); 
			
			$data['Identification[api_key]'] = $pay4laterData['Identification']['api_key'];
			$data['Identification[RetailerUniqueRef]'] = $OrderId;
            $data['Identification[InstallationID]'] = $pay4laterData['Identification']['InstallationID'];
            $data['Finance[Code]'] = $pay4laterData['Finance']['Code'];
            $data['Finance[Deposit]'] = (float)$pay4laterData['Finance']['Deposit'];
			$i=0;
			foreach($pay4laterData['Goods'] as $item){
				$data['Goods['.$i.'][Description]'] = $item['Description'];
				$data['Goods['.$i.'][Price]'] = $item['Price'];
				$data['Goods['.$i.'][Quantity]'] = $item['Quantity'];
				$i++;
			}
		}

		return $data;
	}
		 
	
}
 