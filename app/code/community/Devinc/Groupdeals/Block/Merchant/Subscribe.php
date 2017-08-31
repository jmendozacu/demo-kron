<?php
class Devinc_Groupdeals_Block_Merchant_Subscribe extends Mage_Core_Block_Template
{
	//add groupdeal breadcrumbs
    protected function _prepareLayout()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', array(
                'label'=>Mage::helper('catalog')->__('Home'),
                'title'=>Mage::helper('catalog')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ));
            
            $breadcrumbsBlock->addCrumb('become_merchant', array(
                'label'=>Mage::helper('groupdeals')->__('Become a Merchant'),
                'title'=>Mage::helper('groupdeals')->__('Become a Merchant'),
            ));
        }
        
        return parent::_prepareLayout();
    }
    
}
