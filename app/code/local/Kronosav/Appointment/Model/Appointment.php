<?php
class Kronosav_Appointment_Model_Appointment extends Mage_Core_Model_Abstract
{
    public function _construct()
    {  
		parent::_construct();
        $this->_init('appointment/appointment');
    }  
}
?>