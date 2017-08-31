<?php

/**
 * @package    Magedevgroup_ScrappageScheme
 * @author     Magedevgroup
 * @contacts   https://magedevgroup.com/
 */
class Magedevgroup_ScrappageScheme_Model_Scrap extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('scrappagescheme/scrap');
    }
}
