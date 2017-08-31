<?php
class Kronosav_Appointment_Model_Mysql4_Appointment_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {  
        $this->_init('appointment/appointment');
    }
	
}
?>