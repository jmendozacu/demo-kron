<?php

class Magedevgroup_ProductVideo_Block_Product_View_Video extends Mage_Catalog_Block_Product_View
{

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getVideo()
    {
        $productVideo = $this->getProduct()->getYoutubeVideo();
        $embedUrl = Mage::helper('magedevgroup_productvideo')->convertUrl($productVideo);

        return $embedUrl;
    }
}