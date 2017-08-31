<?php
class Devinc_Groupdeals_Block_Customer_Html_Pager extends Mage_Page_Block_Html_Pager
{	
	//last number pager fix
	public function getLastNum()
    {
        $collection = $this->getCollection();
        $calc = $collection->getPageSize()*$collection->getCurPage();
        if ($calc<=$collection->count()) {
        	return $calc;
        } else {
        	return $collection->count();
        }
    }
	
}