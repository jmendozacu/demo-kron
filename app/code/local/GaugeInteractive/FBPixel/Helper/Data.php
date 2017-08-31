<?php
/**
 *
 * @category   GaugeInteractive
 * @package    GaugeInteractive_FBPixel
 */
class GaugeInteractive_FBPixel_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Config paths for using throughout the code
     */
    const FACEBOOK_PIXEL_ACTIVE                     = 'fbpixel_options/value/facebookpixelactive';
    const FACEBOOK_PIXEL_ID                               = 'fbpixel_options/value/fbpixel_id';
    const ADD_TO_CART_ACTIVE                           = 'fbpixel_options/value/addtocartactive';
    const INITIATE_CHECKOUT_ACTIVE               = 'fbpixel_options/value/iniatecheckoutactive';
    const PURCHASE_ACTIVE                                 = 'fbpixel_options/value/purchaseactive';
    /**
     *
     *
     * @param mixed $store
     * @return string ID
     */
    public function getContainerId($store = null)
    {
        return Mage::getStoreConfig(self::FACEBOOK_PIXEL_ID, $store);
    }

    public function isCartTrackingEnabled($store = null)
    {
         return Mage::getStoreConfigFlag(self::ADD_TO_CART_ACTIVE, $store);
    }

    public function isCheckoutTrackingEnabled($store = null)
    {
         return Mage::getStoreConfigFlag(self::INITIATE_CHECKOUT_ACTIVE, $store);
    }

    public function isPurchaseTrackingEnabled($store = null)
    {
         return Mage::getStoreConfigFlag(self::PURCHASE_ACTIVE, $store);
    }

    /**
     *
     * @param mixed $store
     * @return bool
     */
    public function isFacebookPixelAvailable($store = null)
    {
        return $this->getContainerId($store) && Mage::getStoreConfigFlag(self::FACEBOOK_PIXEL_ACTIVE, $store);
    }
}
