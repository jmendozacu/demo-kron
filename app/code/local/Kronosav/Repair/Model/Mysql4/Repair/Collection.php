<?php
class Kronosav_Repair_Model_Mysql4_Repair_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		// parent::__construct();
		$this->_init('repair/repair');
	}
}