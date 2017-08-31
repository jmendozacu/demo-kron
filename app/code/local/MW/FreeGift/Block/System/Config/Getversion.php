<?php
/**
 * User: Anh TO
 * Date: 12/13/13
 * Time: 2:57 PM
 */

class MW_FreeGift_Block_System_Config_Getversion extends Mage_Adminhtml_Block_System_Config_Form_Field{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return "<i style='color:red'>".(string) Mage::getConfig()->getNode()->modules->MW_FreeGift->version."</i>".(isset($_GET['b']) ? ' by <b>Anh TO.</b>' : '');
    }
}