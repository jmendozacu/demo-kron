<?php

class Simi_Ztheme_ApiController extends Simi_Connector_Controller_Action {

    public function get_bannersAction() {
        $data = $this->getData();
        $phone_type = $data->phone_type;
        if ($phone_type == null || !isset($phone_type))
            $phone_type = "phone";
        $information = Mage::getModel('ztheme/banner')->getBanners($data, $phone_type);
        $this->_printDataJson($information);
    }

    public function get_banners_and_spotAction() {
        $data = $this->getData();
        $phone_type = $data->phone_type;
        if ($phone_type == null || !isset($phone_type))
            $phone_type = "phone";
        $information = Mage::getModel('ztheme/banner')->getBannersAndSpot($data, $phone_type);
        $this->_printDataJson($information);
    }

    public function get_order_spotsAction() {
        $data = $this->getData();
        $phone_type = $data->phone_type;
        if ($phone_type == null || !isset($phone_type))
            $phone_type = "phone";
        $information = Mage::getModel('ztheme/spotproduct')->getSpotProduct($data, $phone_type);
        $this->_printDataJson($information);
    }

    public function get_spot_productsAction() {
        $data = $this->getData();
        $information = Mage::getModel('ztheme/spotproduct')->getSpotProducts($data);
        $this->_printDataJson($information);
    }
    
    public function get_category_treeAction() {
        $data = $this->getData();
        $information = Mage::getModel('ztheme/category')->getCategoryTree($data);
        $this->_printDataJson($information);
    }
    
}
