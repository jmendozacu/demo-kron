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
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml AdminNotification Severity Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Renderer_Couponcolumncsv extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
	public function render(Varien_Object $row)
    {	
		$orderId = $row->getData($this->getColumn()->getIndex());
		$productId = $this->getRequest()->getParam('id');
		$groupdealId = $this->getRequest()->getParam('groupdeals_id');
		$items = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id', $orderId)->addFieldToFilter('product_id', $productId);
		
		$couponCodesArray = array();
		$html = '';	
			
		if (count($items)>0) {
			foreach ($items as $item) {
				$couponCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('groupdeals_id', $groupdealId)->addFieldToFilter('order_item_id', $item->getId());
				if (count($couponCollection)>0) {
					foreach($couponCollection as $coupon){
					    if ($coupon->getStatus()=='sending') {
					    	$couponCodesArray[] = 'Sending';
					    } elseif ($coupon->getStatus()=='complete') {
					    	$couponCodesArray[] = $coupon->getCouponCode();
					    } else if ($coupon->getStatus()=='voided') {
					    	$couponCodesArray[] = 'Voided';
					    } else {
					    	$couponCodesArray[] = 'Coupon Not Sent';
					    }
					} 
				} else {			
					$couponCodesArray[] = 'Coupon Not Generated';	
				}
			}
		}
			
		if (count($couponCodesArray)>0) {
			$html = implode(' // ', $couponCodesArray);		
		}
		
		return $html;
    }
	 
   
}
