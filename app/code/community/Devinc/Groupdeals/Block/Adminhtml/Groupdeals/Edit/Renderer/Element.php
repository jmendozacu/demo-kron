<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Renderer_Element extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    public function render(Varien_Data_Form_Element_Abstract $element) 
	{		
		$elementNote = '';
		if ($element->getNote()!='') {   
			$elementNote = '<p class="note" id="note_coupon_merchant_contact"><span>'.$element->getNote().'</span></p>';
	    }
	    
	    $html = '<tr><td class="label">'.$element->getLabelHtml().'</td><td class="value">'.$element->getElementHtml().$elementNote.'</td><td class="scope-label"><span class="nobr">[GLOBAL]</span></td></tr>';
	    
        return $html;
    }	
	

}
