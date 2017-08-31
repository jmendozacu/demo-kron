<?php
class Kronosav_Repair_Model_Mysql4_Repair extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('repair/repair','repair_id');
	}
}