<?php
class Devinc_Groupdeals_Block_Layer_View extends Mage_Core_Block_Template
{
	protected $_allowedCategoryIds = array();

	public function getItemsHtml($_isMobile = false) 
	{
		if (Mage::app()->getRequest()->getActionName()=='recent') {	
			$listBlock = Mage::getBlockSingleton('groupdeals/product_recent');
		} else if (Mage::app()->getRequest()->getActionName()=='upcoming') {
			$listBlock = Mage::getBlockSingleton('groupdeals/product_upcoming');
		} else {
			$listBlock = Mage::getBlockSingleton('groupdeals/product_list');
		}
		$collection = $listBlock->getLoadedProductCollection(false);    		
        $helper = Mage::helper('catalog/category');
        $categories = $helper->getStoreCategories();
        
        $html = '';
        if (count($categories)>0) {
	    	$html = $this->getCategoriesHtml($categories, $collection, 0, $html, $_isMobile);
	    }
		
		return $html;
	}
	
	public function getCategoriesHtml($_categories, $_collection, $_level, $_html, $_isMobile = false)
	{		
		$_collection->addCountToCategories($_categories);
	    foreach ($_categories as $category) {
	        if ($category->getIsActive()) {	     
				$subcatsHaveProductCount = false;
				$this->_allowedCategoryIds[$category->getId()] = false;
	    	
				$subcatsHtml = '';
		        $children = $category->getChildren();
		        if ($children && $children->count()) {
			        $subcatsHtml = $this->getCategoriesHtml($children, $_collection, ($_level+1), '', $_isMobile);
		        }   
		        
		        if ($category->getProductCount() || $subcatsHtml!='') {		        
			        $this->_allowedCategoryIds[$category->getId()] = true;
			        if ($_isMobile) {
			        	if ($category->getProductCount()) {
			        		$url = $this->getClearUrl();
			        		if (strpos($url, '?') !== false) {
						        $url .= '&cats='.$category->getId();
					        } else {
						        $url .= '?cats='.$category->getId();
						    }
						    $selected = (isset($_GET['cats']) && $_GET['cats']!='' && $category->getId()==$_GET['cats']) ? 'selected="selected" ' : '';
				            $_html .= '<option '.$selected.'value="'.$url.'">'.$category->getName().'</option>';				        
				        } 
				        $_html .= $subcatsHtml;
			        } else {			        	
				        $url = $this->helper('core/url')->getCurrentUrl();
				        if (isset($_GET['cats']) && $_GET['cats']!='') {
				        	$catsArray = explode(',',$_GET['cats']);
				        	if (!in_array($category->getId(),$catsArray)) {
						        $url .= ','.$category->getId();
						    } else {
						    	$key = array_search($category->getId(), $catsArray);
								if (false !== $key) unset($catsArray[$key]);
								if (count($catsArray)>0) { 
								    $url = str_replace($_GET['cats'], implode(',',$catsArray), $url);
								} else {
									$url = $this->getClearUrl();
								}
						    }
						} else {
				        	if (strpos($url, '?') !== false) {
						        $url .= '&cats='.$category->getId();
					        } else {
						        $url .= '?cats='.$category->getId();
						    }						
						}
						
				        $checkbox = ($category->getProductCount()) ? '<input type="checkbox" id="category-nav-'.$category->getId().'" value="'.$category->getId().'" onclick="addCategoryToFilter(\''.$url.'\')" />' : '';
				        $onclick = ($category->getProductCount()) ? ' onclick="$(\'category-nav-'.$category->getId().'\').click();"' : '';
				        $productCount = ($category->getProductCount()) ? ' ('.$category->getProductCount().')' : '';
				        $subcatsHtml = ($subcatsHtml!='') ? '<ol>'.$subcatsHtml.'</ol>' : '';
			            $_html .= '<li><span></span>'.$checkbox.'<a href="javascript:void(0)"'.$onclick.'>'.$category->getName().$productCount.'</a>'.$subcatsHtml.'</li>';
			        }
		        } 
	        }
	    }
	    return $_html;
	}
	
	public function getClearUrl() {
		$currentUrl = $this->helper('core/url')->getCurrentUrl();	
				
		if (strpos($currentUrl, '?cats=') !== false) {
		    $url = substr($currentUrl, 0, strpos($currentUrl, '?cats='));
		} else {
		    $url = substr($currentUrl, 0, strpos($currentUrl, '&cats='));
		}			
		
		return $url;
	}
	
	public function selected() {
		if (isset($_GET['cats']) && $_GET['cats']!='') {
			return $_GET['cats'];
		}
		
		return false;
	}
	
	public function canShowBlock() {
		return ($this->getItemsHtml(true)!='');
	}
}
