<?php
class Vivacity_Locator_Block_Monblock extends Mage_Core_Block_Template
{
     public function methodblock()
     {
        
     $retour='';
     $collection = Mage::getModel('locator/locator')->getCollection()->setOrder('locator_id','asc');
     return $collection;
        
     }
}
