<?php

class Devinc_Groupdeals_Block_Adminhtml_Groupdeals_Edit_Tab_Bundle extends Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle
{
    public function getTabUrl()
    {
        return $this->getUrl('adminhtml/bundle_product_edit/form', array('_current' => true));
    }    

}