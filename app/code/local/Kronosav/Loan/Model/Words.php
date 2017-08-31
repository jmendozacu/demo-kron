<?php
class Kronosav_Loan_Model_Words
{
 	public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('loan')->__('Yes')),            
            array('value'=>2, 'label'=>Mage::helper('loan')->__('No')),                       
        );
    }
 
}