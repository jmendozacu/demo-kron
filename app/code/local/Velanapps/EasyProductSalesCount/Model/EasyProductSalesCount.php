<?php
class Velanapps_EasyProductSalesCount_Model_EasyProductSalesCount extends Mage_Core_Model_Abstract
{
	public function salesCount($id)
	{
		$items = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('product_id', $id);
		$count = 0;
		foreach ($items as $item)
		{

			$count += $item->getQtyOrdered() - $item->getQtyCanceled() - $item->getQtyRefunded();
		}
		return $count+(self::getInstore($id));
	}
	
	public function getStatus($id)
	{
		//return Mage::getModel('catalog/product')->load($id)->getAttributeText('va_product_sales_count_show');
		return Mage::getModel('catalog/product')->load($id)->getVaProductSalesCountShow();
	}
	public function getInstore($id)
	{
		//return Mage::getModel('catalog/product')->load($id)->getAttributeText('va_product_sales_count');
		return Mage::getModel('catalog/product')->load($id)->getVaProductSalesCount();
	}
	public function getProduct()
    {
        if (!Mage::registry('product') && $this->getProductId()) {
            $product = Mage::getModel('catalog/product')->load($this->getProductId());
            Mage::register('product', $product);
        }
        return Mage::registry('product');
    }
}

	