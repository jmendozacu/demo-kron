<?php

class Devinc_Groupdeals_Block_Checkout_Cart_Item_Renderer extends Mage_Checkout_Block_Cart_Item_Renderer
{

    /**
     * Retrieve URL to item Product
     *
     * @return string
     */
    public function getProductUrl()
    {
        $productUrl = parent::getProductUrl();
        $crcId = $this->getItem()->getCrcId();
        
        if (isset($crcId) && $crcId!='' && $crcId!=0) {
        	return $productUrl.'?crc='.$crcId;
        } else {
	        return $productUrl;	        
        }
    }
}
