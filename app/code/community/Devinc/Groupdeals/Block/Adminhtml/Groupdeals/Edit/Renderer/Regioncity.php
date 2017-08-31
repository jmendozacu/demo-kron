<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Renderer_Regioncity extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    public function render(Varien_Data_Form_Element_Abstract $element) 
	{		
		$html = $this->getLayout()->createBlock('groupdeals/adminhtml_groupdeals', 'region_city')->setTemplate('groupdeals/product/renderer/region_city.phtml')->toHtml();
		
        return $html;
    }	
	

}
