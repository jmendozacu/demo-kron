<?php
class Velanapps_Tutorial11_Model_System_Config_Source_Shipping_Flatrateuk
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=> Mage::helper('tutorial11')->__('None')),
            array('value'=>'O', 'label'=>Mage::helper('tutorial11')->__('Per Order')),
            array('value'=>'I', 'label'=>Mage::helper('tutorial11')->__('Per Item')),
        );
    }
}
?>