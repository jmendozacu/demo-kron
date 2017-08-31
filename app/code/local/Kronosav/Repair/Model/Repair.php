<?php 
class Kronosav_Repair_Model_Repair extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('repair/repair');
	}
}