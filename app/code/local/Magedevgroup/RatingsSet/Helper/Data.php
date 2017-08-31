<?php
/**
 * @package    Magedevgroup_RatingsSet
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */

class Magedevgroup_RatingsSet_Helper_Data extends Mage_Core_Helper_Abstract
{
    const RATINGSSET_GLOBAL_DEFAULT = 'ratingsset/general/default';

    /**
     * @return Mage_Core_Model_Config
     */
    public function getDefaultSet()
    {
        $ratingsSet = Mage::getConfig(self::RATINGSSET_GLOBAL_DEFAULT);
        return $ratingsSet;
    }
}