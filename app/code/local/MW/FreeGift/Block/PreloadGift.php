<?php
/**
 * User: Anh TO
 * Date: 12/12/13
 * Time: 3:34 PM
 */

class MW_FreeGift_Block_PreloadGift extends Mage_Core_Block_Template{
    public function _construct()
    {
        $this->setTemplate('mw_freegift/preload_gift.phtml');
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