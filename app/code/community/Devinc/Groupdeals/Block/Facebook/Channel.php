<?php
/**
 * Facebook channel block
 * 
 * @author     Ivan Weiler <ivan.weiler@gmail.com>
 */
class Devinc_Groupdeals_Block_Facebook_Channel extends Devinc_Groupdeals_Block_Facebook_Template
{

    protected function _toHtml()
    {
		return '<script src="'.($this->isSecure() ? 'https://' : 'http://').'connect.facebook.net/'.($this->getData('locale') ?  $this->getData('locale') : $this->getLocale()).'/all.js"></script>';
    }

}