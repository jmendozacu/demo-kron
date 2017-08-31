<?php

require_once('lib/simpledomhtml/simple_html_dom.php');

class Velan_Pricecompare_IndexController extends Mage_Core_Controller_Front_Action {
	
	public function indexAction(){
		
		$this->priceCompareUpdate();
		
	}
	
	
	public function priceCompareUpdate(){
		
		$logOutput = "";
		
		$products = Mage::getModel('catalog/product')
					->getCollection()
					->addAttributeToSort('entity_id', 'ASC')
					->addAttributeToSelect('*');
		$productsCount = $products->getSize();
		$hoursToCompleteCycle = 168; //Complete updating all products within 1 week time
		$productCountToUpdateNow = $productsCount / $hoursToCompleteCycle;
		$productCountToUpdateNow = (int)ceil($productCountToUpdateNow);
		
		$productUpdateData = Mage::getModel("pricecompare/productupdate")->getCollection()->getFirstItem();
		$lastCountUpdated = $productUpdateData->getLastUpdatedCount(); //Number of products updated so far
		$currentCountUpdated = $lastCountUpdated + $productCountToUpdateNow;
		$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
		$collection->getSelect()->limit($productCountToUpdateNow,$lastCountUpdated);
		
		/*
		$productIds = array(231,232,233);
		$prods = Mage::getModel('catalog/product')
				->getCollection()
				->addAttributeToSelect('*')
				->addAttributeToFilter('entity_id', array('in' => $productIds));
		*/
		
		foreach($collection as $prod){
			
			$exceptionalLink = $prod->getPriceurlExceptionalAvCoUk();
			$junoLink = $prod->getPriceurlJunoCoUk();
			$superfiLink = $prod->getPriceurlSuperfiCoUk();
			
			unset($lowestPrice);
			unset($ourPrice);
			unset($productPriceOld);
			unset($productPriceNew);
			
			unset($exceptionalPrice);
			unset($junoPrice);
			unset($superfiPrice);
			
			$productPriceOld = $prod->getPrice();
			$competitorSiteDetails = array();
			
			if(!empty($exceptionalLink))
				$competitorSiteDetails["url_exceptional_av_co_uk"] = $this->getCompetitorSiteDetails($exceptionalLink, ".product-options-bottom * .price", 0);
			
			if(!empty($junoLink))
				$competitorSiteDetails["url_juno_co_uk"] = $this->getCompetitorSiteDetails($junoLink, ".text_cta", 0);
			
			if(!empty($superfiLink))
				$competitorSiteDetails["url_superfi_co_uk"] = $this->getCompetitorSiteDetails($superfiLink, ".webPriceLabel", 0);
			
			$html = new simple_html_dom();
			$prices = array();
			
			foreach ($competitorSiteDetails as $key => $competitorSiteDetail){
				$currentUrl = $competitorSiteDetail['url'];
				if(!empty($currentUrl)){
					$currentSelector = $competitorSiteDetail['selector'];
					$currentElementPosition = $competitorSiteDetail['element_position'];
					$priceVal = $this->fetchPrice($html, $currentUrl, $currentSelector, $currentElementPosition);
					array_push($prices, $priceVal);
				}
			}
			
			if(!empty($prices)){
				
				$lowestPrice = min($prices);
				$ourPrice = $lowestPrice - 1;
				
				if($productPriceOld > $ourPrice){
					
					$newPrice = $ourPrice;
					$prod->setPrice($newPrice);
					$prod->save();
					$productPriceNew = $prod->getPrice();
					echo "<br /><br />Product " . $prod->getName() . " updated with new price " . $productPriceNew;
					
				}
				
			}
			
			$productName = $prod->getName();
			$productId = $prod->getId();
			
			$exceptionalPrice = $prices[0];
			$junoPrice = $prices[1];
			$superfiPrice = $prices[2];
			
$logOutput .= <<<EOH
	
	
	Product Id :  {$productId}
	Product Name :  {$productName}
	
	Exceptional Link : {$exceptionalLink}
	Juno Link : {$junoLink}
	Superfi Link : {$superfiLink}
	
	Exceptional Price : {$exceptionalPrice}
	Juno Price : {$junoPrice}
	Superfi Price : {$superfiPrice}
	
	Lowest Price =  {$lowestPrice}
	Our Price = {$ourPrice}
	Product Price = {$productPriceOld}
	Updated Product Price = {$productPriceNew}
	
	
EOH;
		
		}
		
		//$data = array("last_updated_count" => $currentCountUpdated);
		//$productUpdateData->addData($data)->save();
		
		Mage::log($logOutput, null, 'pricecompare.log');
		
	}
	
	private function fetchPrice($html, $link, $selector, $elementPosition){
			
			$html = new simple_html_dom();
			$html->load_file($link);
			$fileContents = file_get_contents($link);
			
			$html->load($fileContents);
			
			$priceElements = $html->find($selector);
			$priceString = $priceElements[$elementPosition]->innertext;
			$finalPrice = $this->removeUnwantedStrings($priceString);
			
			return $finalPrice;
			
			if($finalPrice)
				return $finalPrice;
			else 
				return null;
		
	}
	
	private function removeUnwantedStrings($priceString){
		
		$prc = str_replace("&#163;", "", $priceString);
		$priceString = preg_replace('/[^a-zA-Z0-9_.]/', '', $prc);
		$priceString = str_replace("OurPrice", "", $priceString);
		$priceString = str_replace("$", "", $priceString);
		$priceFloat = (float)$priceString;
		
		return $priceFloat;
		
	}
	
	private function getCompetitorSiteDetails($url, $selector, $elementPosition){
		
		$competitorSiteDetails = array(
			"url" => $url,
			"selector" => $selector,
			"element_position" => $elementPosition
		);
		
		return $competitorSiteDetails;
		
	}
	
}
