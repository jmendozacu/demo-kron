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
class ModuleMart_Brands_Block_Adminhtml_Brand_Renderer_Logo extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
     /**
	 * @access public
	 * @param array $row
	 */
	 public function render(Varien_Object $row)
    {
        if($row->getData($this->getColumn()->getIndex())==""){
            return "";
        }
        else{
            $html = '<img ';
            $html .= 'id="' . $this->getColumn()->getId() . '" ';
            $html .= 'width="45" ';
            $html .= 'height="45" ';
            $html .= 'src="'.Mage::getBaseUrl("media") . 'brand/file/'.$row->getData($this->getColumn()->getIndex()) . '"';
            $html .= 'class="grid-image ' . $this->getColumn()->getInlineCss() . '"/>';
            
            return $html;
        }
    }
}