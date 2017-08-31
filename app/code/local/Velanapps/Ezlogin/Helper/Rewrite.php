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
class Velanapps_Ezlogin_Helper_Rewrite extends Mage_Core_Helper_Abstract
{
	/**
	Function for Magento core url re write collection.
	Input  : Custom social account url, Ezlogin social account url.
	Output : Returns Magento core url re write collection.
	*/
	public function ezLoginUrlRewrite($socialAccountUrl, $customUrl)
	{	
		//Getting current store id.
		$storeId = Mage::app()->getStore()->getId();
		
		$urlRewrite = Mage::getModel('core/url_rewrite');
				      $urlRewrite->setStoreId($storeId)
				      ->setIdPath($socialAccountUrl)
				      ->setRequestPath($socialAccountUrl)
				      ->setTargetPath($customUrl)
				      ->setIsSystem(true)
				      ->save();
						 
		return;
	}
	
	/**
	Function for getting Magento core url filter
	Input  : Each social login id path url, Ezlogin target path url.
	Output : Returns Magento core url Array.
	*/
	public function ezLoginUrlFilter($idPathUrl)
	{
		//Getting current store id.
		$currentStoreId = Mage::app()->getStore()->getId();
		
		$urlFilter = Mage::getModel('core/url_rewrite')->getCollection()
					 ->addFieldToFilter('store_id', $currentStoreId)
					 ->addFieldToFilter('id_path', $idPathUrl)
					 ->getData();
		
		return $urlFilter[0];
	}
	
}
