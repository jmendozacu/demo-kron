<?php
class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Renderer_Radio extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
	public function render(Varien_Object $row)
    {				
		return '<input type="radio" class="radio" onClick="setProduct('.$row->getId().');" value="'.$row->getId().'" id="products_grid_radio'.$row->getId().'" name="products_grid_radio">';	
    }
	 
   
}
