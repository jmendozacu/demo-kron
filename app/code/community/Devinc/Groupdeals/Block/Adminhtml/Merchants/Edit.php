<?php

class Devinc_Groupdeals_Block_Adminhtml_Merchants_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'groupdeals';
        $this->_controller = 'adminhtml_merchants';
        
        //update buttons
		$this->_removeButton('reset');	
        $this->_updateButton('save', 'label', Mage::helper('groupdeals')->__('Save Merchant'));
        $this->_updateButton('delete', 'label', Mage::helper('groupdeals')->__('Delete Merchant'));
		if(Mage::getModel('groupdeals/merchants')->isMerchant()){
			$this->_removeButton('back');
			$this->_removeButton('delete');
			$this->_removeButton('save');
		}	
    }

    protected function _prepareLayout()
    {
        $this->_addButton('save_and_continue', array(
            'label'     => Mage::helper('customer')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit(\''.$this->_getSaveAndContinueUrl().'\')',
            'class'     => 'save'
        ), 10);

        return parent::_prepareLayout();
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'  => true,
            'back'      => 'edit',
            'tab'       => '{{tab_id}}'
        ));
    }

    public function getHeaderText()
    {
        if( Mage::registry('merchant_data') && Mage::registry('merchant_data')->getId() ) {
        	$storeId = $this->getRequest()->getParam('store', 0);
        	$name = Mage::getModel('license/module')->getDecodeString(Mage::registry('merchant_data')->getName(), $storeId);
            return $name;
        } else {
            return Mage::helper('groupdeals')->__('New Merchant');
        }
    }
}