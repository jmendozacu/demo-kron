<?php

class Velanapps_Watermark_Model_Mysql4_Watermark extends Mage_Core_Model_Resource_Db_Abstract{
	public function _construct()
	{
		$this->_init('watermark/watermark', 'id');
	}
}

?>