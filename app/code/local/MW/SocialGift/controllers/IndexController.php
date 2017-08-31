<?php
class MW_SocialGift_IndexController extends Mage_Core_Controller_Front_Action {

    public function _construct()
    {
        parent::_construct();
        if(!Mage::helper('mw_socialgift')->isEnabled()){
            Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
        }
        $request = $this->getRequest();
    }

    /**
     * Renders Home page
     */
    public function indexAction() {
        // load layout, set title
        $this->loadLayout();
        // $headBlock = $this->getLayout()->getBlock('head')->setTitle("Simple News");
        // $listBlock = $this->getLayout()->getBlock('simplenews.list');
        $this->renderLayout();
    }

    public function ajaxAction() {

        $result = array();      
        $session = Mage::getSingleton('checkout/session');
        $session_checkout = Mage::getSingleton('checkout/session');
        
        $post_id = $this->getRequest()->getPost('post_id');
        $product_id = $this->getRequest()->getPost('product_id');

        $product_shared = array();
        array_push($product_shared, $product_id);

        if (isset($post_id)) {
            $session->setSocialGiftStatus('shared');
            $session_checkout->setSocialProductShared($product_shared);
            $session->setNumberSocialGift(0);
        } 
        else{
            $session->setSocialGiftStatus('note_share');
        }
        // $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    public function getGiftAction()
    {
        $block = $this->getLayout()->createBlock('mw_socialgift/socialgift')->setTemplate('mw_socialgift/socialgift_gift.phtml');
        // echo($block->toHtml());
        $result = array("html"=>$block->toHtml());
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

        return $result;
    }

}