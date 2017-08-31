<?php
/**
 *
 * @category   GaugeInteractive
 * @package    GaugeInteractive_FBPixel
 */
class GaugeInteractive_FBPixel_Block_Fbp extends Mage_Core_Block_Template
{
    /**
     * Get Facebook Pixel id
     *
     * @return string ID
     */
    public function getContainerId()
    {
        return Mage::helper('fbpixel')->getContainerId();
    }

    public function isCartTrackingEnabled()
    {
        return Mage::helper('fbpixel')->isCartTrackingEnabled();
    }

    public function isCheckoutTrackingEnabled()
    {
         return Mage::helper('fbpixel')->isCheckoutTrackingEnabled();
    }

    public function isPurchaseTrackingEnabled()
    {
         return Mage::helper('fbpixel')->isPurchaseTrackingEnabled();
    }
    /**
     *
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!Mage::helper('fbpixel')->isFacebookPixelAvailable()) {
            return '';
        }
        return parent::_toHtml();

        //get enabled, return isFacebookPixelAvailable();


    }
}
