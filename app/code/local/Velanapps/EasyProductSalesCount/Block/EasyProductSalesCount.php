<?php
class Velanapps_EasyProductSalesCount_Block_EasyProductSalesCount extends Mage_Core_Block_Template
{
	public function soldProducts($id)
    {
        return Velanapps_EasyProductSalesCount_Model_EasyProductSalesCount::salesCount($id);
		// $items = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('product_id', $id);
		// $count = 0;
		// foreach ($items as $item)
		// {

			// $count += $item->getQtyOrdered() - $item->getQtyCanceled() - $item->getQtyRefunded();
		// }
		// return $count;
	}
	public function status($id)
	{
		return Velanapps_EasyProductSalesCount_Model_EasyProductSalesCount::getStatus($id);
		// return Mage::getModel('catalog/product')->load($id)->getAttributeText('va_product_sales_count_show');
	}
	public function getProductDetail()
	{
		return Velanapps_EasyProductSalesCount_Model_EasyProductSalesCount::getProduct();
	}
	public function getAccess()
	{
		return Velanapps_EasyProductSalesCount_Helper_Data::getActivation();
	}
}

?>