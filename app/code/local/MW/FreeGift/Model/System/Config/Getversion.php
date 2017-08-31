<?php
/**
 * User: Anh TO
 * Date: 12/13/13
 * Time: 2:36 PM
 */

class MW_FreeGift_Model_System_Config_Getversion extends Mage_Core_Model_Abstract{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' =>  (string) Mage::getConfig()->getNode()->modules->MW_FreeGift->version)
        );
    }
}