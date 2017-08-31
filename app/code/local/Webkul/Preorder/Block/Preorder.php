<?php
class Webkul_Preorder_Block_Preorder extends Mage_Core_Block_Template
{
	public function _prepareLayout() {
		return parent::_prepareLayout();
    }
    
    public function getPreorder() { 
        if (!$this->hasData('preorder')) {
            $this->setData('preorder', Mage::registry('preorder'));
        }
        return $this->getData('preorder');   
    }
}