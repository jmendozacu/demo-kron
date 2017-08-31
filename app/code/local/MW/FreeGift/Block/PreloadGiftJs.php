<?php
/**
 * User: Anh TO
 * Date: 12/13/13
 * Time: 3:47 PM
 */

class MW_FreeGift_Block_PreloadGiftJs extends Mage_Core_Block_Template{
    public function _construct()
    {
        $this->setTemplate('mw_freegift/preload_gift_js.phtml');
    }
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    public function _toHtml()
    {
        if (!Mage::getStoreConfig('freegift/config/enabled'))
            return '';
        $html = $this->renderView();
        return $html;
    }
}