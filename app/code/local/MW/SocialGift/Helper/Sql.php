<?php
class MW_SocialGift_Helper_Sql extends Mage_Core_Helper_Abstract{
    public function checkTableStatus($table_name){
        $conn_read  = Mage::getSingleton('core/resource')->getConnection('core_read');
        $db_name =  end(Mage::getConfig()->getResourceConnectionConfig('default_setup')->dbname);
        $sql = "SHOW TABLES FROM ".$db_name."";

        foreach($conn_read->fetchAll($sql) as $tbl){
            $_table_name = end(array_values($tbl));
            if($table_name == $_table_name){
                return true;
            }
        }
        return false;
    }
    public function checkColumnExist($table_name, $col_name){
        $conn_read  = Mage::getSingleton('core/resource')->getConnection('core_read');
        $db_name =  end(Mage::getConfig()->getResourceConnectionConfig('default_setup')->dbname);
        $sql = "SHOW COLUMNS FROM $table_name";
        foreach($conn_read->fetchAll($sql) as $col){
            $_col_name = $col['Field'];
            if($col_name == $_col_name){
                return true;
            }
        }
        return false;
    }
}
?>