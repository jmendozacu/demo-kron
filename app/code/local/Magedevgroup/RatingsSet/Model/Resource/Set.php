<?php
/**
 * @package    Magedevgroup_RatingsSet
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

class Magedevgroup_RatingsSet_Model_Resource_Set extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('magedevgroup_ratingsset/set', 'entity_id');
    }

}