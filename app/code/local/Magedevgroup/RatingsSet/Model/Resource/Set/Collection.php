<?php
/**
 * @package    Magedevgroup_RatingsSet
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

class Magedevgroup_RatingsSet_Model_Resource_Set_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected  function _construct()
    {
        parent::_construct();

        $this->_init(
            'magedevgroup_ratingsset/set',
            'magedevgroup_ratingsset/set'
        );
    }
}