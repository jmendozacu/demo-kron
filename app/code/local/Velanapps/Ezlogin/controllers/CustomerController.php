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

//Include the Magento Admin Html core files for Overriding.
include_once("Mage/Adminhtml/controllers/CustomerController.php");
class Velanapps_Ezlogin_CustomerController extends Mage_Adminhtml_CustomerController
{	
	/**
	Function for Customer Account Mass Delete Action from Admin Panel.
	Input  : Customer ids to be deleted are given as input
	Output : Customer Accounts in Magento Customer Table and EzLogin Customer Table are deleted. 
	*/
    public function massDeleteAction()
    {	
		$customersIds = $this->getRequest()->getParam('customer');
		
        if(!is_array($customersIds)) 
		{
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } 
		else
		{
            try {
                $customer = Mage::getModel('customer/customer');
                foreach ($customersIds as $customerId) 
				{
                    $customer->reset()
                        ->load($customerId)
                        ->delete();
                       
					//Checking ezlogin Customer table.
					$getEzLoginCustomer = Mage::getModel('ezlogin/ezlogincustomers')->getCollection();
					$getEzLoginCustomer->addFieldToFilter('mage_customer_id', $customerId);
					$getEzLoginCustomerData = $getEzLoginCustomer->getData(); 
					
					if(isset($getEzLoginCustomerData))
					{	
						foreach($getEzLoginCustomerData as $ezLoginCustomers)
						{	
							//Delete ezlogin Customer's Records.
							$deleteEzLoginCustomer = Mage::getModel('ezlogin/ezlogincustomers');
							$deleteEzLoginCustomer->load($ezLoginCustomers['customer_id']);
							$deleteEzLoginCustomer->delete();
						}
					}
						
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were deleted.', count($customersIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }
	
}
