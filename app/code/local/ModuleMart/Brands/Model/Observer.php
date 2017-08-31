<?php
 /**
 * ModuleMart_Brands extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Module-Mart License
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.modulemart.com/license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to modules@modulemart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * versions in the future. If you wish to customize this extension for your
 * needs please refer to http://www.modulemart.com for more information.
 *
 * @category   ModuleMart
 * @package    ModuleMart_Brands
 * @author-email  modules@modulemart.com
 * @copyright  Copyright 2014 © modulemart.com. All Rights Reserved
 */
class ModuleMart_Brands_Model_Observer extends Mage_Core_Model_Abstract{
	
	public function brandsOnTop(Varien_Event_Observer $observer)
	{
		
		$isOnTop = Mage::getStoreConfigFlag('brands/brand/is_on_top');
		
		if($isOnTop) {
		
			$menu = $observer->getMenu();
			$tree = $menu->getTree();
			$collection = Mage::getResourceModel('brands/brand_collection')
							->addStoreFilter(Mage::app()->getStore())
							->addFilter('status', 1)
							->addFilter('is_on_top', 1);
		
			$node = new Varien_Data_Tree_Node(array(
					'name'   => 'All Brands',
					'id'     => 'brands',
					'url'    => Mage::getUrl('brands/'),
			), 'id', $tree, $menu);
			$menu->addChild($node);
		
			foreach ($collection as $brands) {
				$tree = $node->getTree();
				$data = array(
					'name'   => $brands->getBrandName(),
					'id'     => 'brand-node-'.$brands->getUrlKey(),
					'url'    => Mage::getUrl($brands->getUrlKey()),
				);
				$subNode = new Varien_Data_Tree_Node($data, 'id', $tree, $node);
				$node->addChild($subNode);
			}
		}
	}
}