<?php
/**
 * Created by PhpStorm.
 * User: manhnt
 * Date: 10/24/14
 * Time: 3:06 PM
 */
class MW_FreeGift_Model_Social extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('freegift/social');
    }
    public function saveSocial($data){
        $social_model = Mage::getModel('freegift/social');
        $social_model->setData($data)->save();
    }
    public function getStatusSocial(){
        $social_model = Mage::getModel('freegift/social')->getCollection()->getData();
        return $social_model[0]['enable_social'];
    }
    public function getCustomStatus(){
        $social_model = Mage::getModel('freegift/social')->getCollection()->getData();
        return $social_model[0];
    }

}