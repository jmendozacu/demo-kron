<?php
 
class Kronosav_Loan_Block_Product extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{  
 	public function render(Varien_Object $row)
	{
			//$productId =  $row->getData($this->getColumn()->getIndex());
			$id =  $row->getData('loan_id');
			$loanProducts = Mage::getModel('loan/product')->getCollection()
								->addFieldToFilter("loan_id",$id);
				$selectedProducts = $loanProducts->getData(); 
				foreach($selectedProducts as $product)
				{
					$productId = $product['product_id'];
					$collection=Mage::getModel('catalog/product')->load($productId);
					$Names[]=$collection->getData('name');
				}
				
				$productName = implode(",",$Names);
				return $productName;

	}
}