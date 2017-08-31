<?php
/**
 * Facebook source locale model
 * 
 * @author     Ivan Weiler <ivan.weiler@gmail.com>
 */
class Devinc_Groupdeals_Model_Source_Locale
{
    public function toOptionArray()
    {
        return Mage::getModel('groupdeals/facebook_locale')->getOptionLocales();
    }
 
}