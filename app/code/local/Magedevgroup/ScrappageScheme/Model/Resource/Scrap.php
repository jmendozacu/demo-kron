<?php

/**
 * @package    Magedevgroup_ScrappageScheme
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_ScrappageScheme_Model_Resource_Scrap extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('scrappagescheme/scrap', 'scrap_id');
    }

    /**
     * TRUNCATE TABLE magedevgroup_srappagescheme
     *
     * @return $this
     */
    public function truncate()
    {
        $this->_getWriteAdapter()->query('TRUNCATE TABLE ' . $this->getMainTable());

        return $this;
    }
}
