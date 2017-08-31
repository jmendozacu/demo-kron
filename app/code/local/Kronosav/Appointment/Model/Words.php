<?php
class Kronosav_Appointment_Model_Words
{
 	public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('appointment')->__('Yes')),            
            array('value'=>2, 'label'=>Mage::helper('appointment')->__('No')),                       
        );
    }
 
}