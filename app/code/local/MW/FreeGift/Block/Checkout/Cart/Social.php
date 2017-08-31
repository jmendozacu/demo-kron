<?php
class MW_FreeGift_Block_Checkout_Cart_Social extends Mage_Core_Block_Template
{
    public function getCustomStatus(){
        $rules = Mage::getSingleton('checkout/session')->getRulegifts();
        $enable_social = false;
        $enable_google = false;
        $enable_like_fb = false;
        $enable_share_fb = false;
        $enable_twitter = false;

        $default_message = '';

        foreach($rules as $rule){
            if($rule['enable_social'] == 1) $enable_social = true;
            if($rule['google_plus'] == 1) $enable_google = true;
            if($rule['like_fb'] == 1) $enable_like_fb = true;
            if($rule['share_fb'] == 1) $enable_share_fb = true;
            if($rule['twitter'] == 1) $enable_twitter = true;
            if($default_message == '') {
                $default_message = $rule['default_message'];
            }
        }
        $result = array('enable_social'=>$enable_social,'google_plus'=>$enable_google,'default_message'=>$default_message,
                        'like_fb'=>$enable_like_fb,'share_fb'=>$enable_share_fb,'twitter'=>$enable_twitter);

        return $result;
    }

    public function getProductId(){
        $ids = Mage::getSingleton('checkout/session')->getProductgiftid();
        $url =  Mage::getModel('catalog/product')->load($ids[0])->getProductUrl();
        return $url;
    }
}