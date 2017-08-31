<?php
class Kronosav_Appointment_Model_Mysql4_Appointment extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {  
        $this->_init('appointment/appointment', 'appointment_id');
    }  
}
?>