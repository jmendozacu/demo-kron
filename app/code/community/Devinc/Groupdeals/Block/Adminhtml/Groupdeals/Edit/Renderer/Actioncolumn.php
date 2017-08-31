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
class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Renderer_Actioncolumn extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
			
		$emailCouponText = 'Email Coupon(s)';
		
		$itemId = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('order_id', $orderId)->addFieldToFilter('product_id', $this->getRequest()->getParam('id'))->getFirstItem()->getId();	
		if ($itemId) {
		    $couponsCollection = Mage::getModel('groupdeals/coupons')->getCollection()->addFieldToFilter('order_item_id', $itemId)->addFieldToFilter('groupdeals_id', $this->getRequest()->getParam('groupdeals_id'))->addFieldToFilter('status', 'complete');
		    if (count($couponsCollection)>0) {		
		        foreach ($couponsCollection as $coupon) {
		        	$emailCouponText = 'Resend Coupon(s)';
		        } 
		    }											
		}
		
		$product = Mage::getModel('catalog/product')->load($this->getRequest()->getParam('id'));
		
		$html = '<select onchange="orderAction(this)" class="action-select" id="action-select2">';
		$html .= 	'<option value=""></option>';
		$html .= 	'<option value="'.Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id'), 'store' => $this->getRequest()->getParam('store', 0), 'order_id' => $orderId)).'">View Order</option>';
		if ($product->isVirtual()) {
		$html .=		'<option value="'.Mage::helper('adminhtml')->getUrl('*/*/emailCoupon', array('groupdeals_id' => $this->getRequest()->getParam('groupdeals_id'), 'id' => $this->getRequest()->getParam('id'), 'store' => $this->getRequest()->getParam('store', 0), 'order_id' => $orderId)).'">'.$emailCouponText.'</option>';
		}
		$html .=	'</select>';	
				
		return $html;	
	}
	
}

