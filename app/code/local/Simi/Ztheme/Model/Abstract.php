<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Simi_Ztheme_Model_Abstract extends Simi_Connector_Model_Abstract {
    public function getConfig($value) {
        return Mage::getStoreConfig("ztheme/general/" . $value);
    }
}
