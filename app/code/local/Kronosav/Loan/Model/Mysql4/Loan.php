<?php
class Kronosav_Loan_Model_Mysql4_Loan extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {  
        $this->_init('loan/loan', 'loan_id');
    }  
}
?>