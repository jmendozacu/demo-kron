<?php
class Kronosav_Loan_Model_Loan extends Mage_Core_Model_Abstract
{
    public function _construct()
    {  
		parent::_construct();
        $this->_init('loan/loan');
    }  
}
?>