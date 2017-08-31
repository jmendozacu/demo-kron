<?php

	class Webkul_Preorder_IndexController extends Mage_Core_Controller_Front_Action {

		public function checkPreOrderAction() {
			$flag=0;
			$info = array();
			$helper = Mage::helper("preorder");
			$wholedata = $this->getRequest()->getParams();
			$productId = $wholedata['product_id'];
			$product = Mage::getModel('catalog/product')->load($productId);
			$value = $wholedata['value'];
			foreach ($value as $item) {
				list($attributeId, $optionValue) = explode("~", $item);
				$attributesInfo[$attributeId] = $optionValue;
			}
			$pro = $product->getTypeInstance(true)->getProductByAttributes($attributesInfo, $product);
			$attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
			$pricesByAttributeValues = array();
			$basePrice = $product->getFinalPrice();
			foreach ($attributes as $attribute){
				$prices = $attribute->getPrices();
				foreach ($prices as $price) {
					if ($price['is_percent']) { 
						$pricesByAttributeValues[$price['value_index']] = (float)$price['pricing_value'] * $basePrice / 100;
					}
					else { 
						$pricesByAttributeValues[$price['value_index']] = (float)$price['pricing_value'];
					}
				}
			}
			if($helper->isPreorder($pro->getId())) {
				$flag=1;
			}
			$finalPrice = $helper->getPrice($productId);
			foreach ($attributesInfo as $key => $value) {
				if(array_key_exists($value, $pricesByAttributeValues)) {
					$finalPrice+=$pricesByAttributeValues[$value];
				}
			}
			if($flag == 1) {
				$result="<div class='wk-preorder-message-block'>";
				$result.=$helper->getPreordetAmountHtml($productId,$finalPrice);
				$result.= Mage::helper('preorder')->getPreorderCustomMessageHtml();
				$result.= Mage::helper('preorder')->getAdditionalMessage($productId, $finalPrice);
				$result.= "</div>";

				$arr = array('preorder'=>1, 'msg'=>$result);
			} else {
				$arr = array('preorder'=>0, 'msg'=>"");
			}
			echo json_encode($arr);
			die;
		}
	}