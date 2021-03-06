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
class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Renderer_Couponcolumn extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
		
		$html = '';
		
		if (count($items)>0) {
			foreach ($items as $item) {
				$couponCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('groupdeals_id', $groupdealId)->addFieldToFilter('order_item_id', $item->getId());
				if (count($couponCollection)>0) {
					foreach($couponCollection as $coupon){
					    if ($coupon->getStatus()=='sending') {
					    	$html .= 'Sending<br/>';
					    } elseif ($coupon->getStatus()=='complete') {
					    	$previewCouponUrl = Mage::helper('adminhtml')->getUrl('*/*/previewCoupon', array('_current' => true, 'coupon_id' => $coupon->getId()));
					    	$redeemCouponUrl = Mage::helper('adminhtml')->getUrl('*/*/redeem', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id'), 'store' => $this->getRequest()->getParam('store', 0), 'coupon_id' => $coupon->getId()));
					    	if ($coupon->getRedeem()=='used') {
					    	    $html .= '<strong>'.$coupon->getCouponCode().'</strong> <br/><a href="#" onclick="window.open(\''.$previewCouponUrl.'\', \'\', \'width=715,height=1000,resizable=1,scrollbars=1\')">View</a> || USED<br/>';
					    	} else {
					    	    $html .= '<strong>'.$coupon->getCouponCode().'</strong> <br/><a href="#" onclick="window.open(\''.$previewCouponUrl.'\', \'\', \'width=715,height=1000,resizable=1,scrollbars=1\')">View</a> || <a href="'.$redeemCouponUrl.'">Redeem</a><br/>';
					    	}
					    } else if ($coupon->getStatus()=='voided') {
					    	$html .= 'Voided<br/>';
					    } else {
					    	$html .= 'Coupon Not Sent<br/>';
					    }
					} 
				} else {			
					$html .= 'Coupon Not Generated<br/>';	
				}
			}
		}
		
		return $html;
    }
	 
   
}
