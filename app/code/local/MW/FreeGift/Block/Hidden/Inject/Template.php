<?php
/**
 * User: Anh TO
 * Date: 3/24/14
 * Time: 5:37 PM
 */

class MW_FreeGift_Block_Hidden_Inject_Template extends Mage_Core_Block_Template{
    public function _toHtml()
    {
        if (!Mage::getStoreConfig('freegift/config/enabled'))
            return '';
        $html = $this->renderView();
        return $html;
    }
}