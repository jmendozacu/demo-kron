<?php

/**
 * @package    Magedevgroup_ScrappageScheme
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class  Magedevgroup_ScrappageScheme_Model_Resource_Scrap_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('scrappagescheme/scrap', 'scrappagescheme/scrap');
    }
}
