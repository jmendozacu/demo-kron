<?php
class Kronosav_Loan_Model_Mysql4_Loan_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {  
        $this->_init('loan/loan');
    }
	
}
?>