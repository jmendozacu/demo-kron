<?php

class Velanapps_Watermark_Helper_Image extends Mage_Catalog_Helper_Image
{

	public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile=null)
	{
        $this->_reset();
        $this->_setModel(Mage::getModel('catalog/product_image'));
        $this->_getModel()->setDestinationSubdir($attributeName);
        $this->setProduct($product);
		
		if($product->getWatermark())
		{
			$path = $this->setWatermark(
				'default/scrappage_scheme.png'
			);			
			$this->setWatermarkImageOpacity(
				Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_imageOpacity")
			);
			$this->setWatermarkPosition(
				Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_position")
			);
			$this->setWatermarkSize(
				Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_size")
			);
		}
        if ($imageFile) {
            $this->setImageFile($imageFile);
        } else {
            // add for work original size
            //$this->_getModel()->setBaseFile($this->getProduct()->getData($this->_getModel()->getDestinationSubdir()));
        }
        return $this;
	}
}