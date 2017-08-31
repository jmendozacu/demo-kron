<?php

class Devinc_Groupdeals_Block_Adminhtml_Merchants_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('merchants_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('groupdeals')->__('Merchants Information'));
    }
    
    protected function _beforeToHtml()
    {	    	
        $this->addTab('general_section', array(
            'label'     => Mage::helper('groupdeals')->__('General'),
            'title'     => Mage::helper('groupdeals')->__('General'),
            'content'   => $this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_tab_general')->toHtml(),
        ));
    	  
        $this->addTab('address_section', array(
            'label'     => Mage::helper('groupdeals')->__('Address'),
            'title'     => Mage::helper('groupdeals')->__('Address'),
            'content'   => $this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_tab_address')->toHtml(),
        ));
    	  
        if(!Mage::getModel('groupdeals/merchants')->isMerchant()){	  
    		$this->addTab('payment_section', array(
    		    'label'     => Mage::helper('groupdeals')->__('Payment Information'),
    		    'title'     => Mage::helper('groupdeals')->__('Payment Information'),
    		    'content'   => $this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_tab_payment')->toHtml(),
    		));
    		
    		$this->addTab('account_section', array(
    		    'label'     => Mage::helper('groupdeals')->__('Merchant Account'),
    		    'title'     => Mage::helper('groupdeals')->__('Merchant Account'),
    		    'content'   => $this->getLayout()->createBlock('groupdeals/adminhtml_merchants_edit_tab_account')->toHtml(),
    		));
        }  
    	  
        return parent::_beforeToHtml();
    }

}