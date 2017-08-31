<?php 
class Kronosav_Repair_Block_Repair extends Mage_Core_Block_Template
{
	
	public function getStatus($repair_id)
	{
		$collection = Mage::getModel('repair/repair')->load($repair_id);
		return $collection->getStatus();
	}
}