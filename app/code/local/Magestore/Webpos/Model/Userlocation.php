<?php

/**
 * Created by PhpStorm.
 * User: Quoc Viet
 * Date: 07/07/2015
 * Time: 9:53 SA
 * 
 * Edited by NetBeans.
 * User: Daniel
 * Date: 21/01/2016
 * Time: 05:49 PM
 */
class Magestore_Webpos_Model_Userlocation extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('webpos/userlocation');
    }

    public function toOptionArray() {
        $options = array();
        $locationCollection = $this->getCollection();
        if ($locationCollection->getSize() > 1) {
            $options = array('' => '---Select Location---');
        }
        foreach ($locationCollection as $location) {
            $key = $location->getLocationId();
            $value = $location->getDisplayName();
            $options [$key] = $value;
        }
        return $options;
    }

}
