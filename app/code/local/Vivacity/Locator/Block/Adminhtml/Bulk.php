<?php
class Vivacity_Locator_Block_Adminhtml_Bulk extends Mage_Adminhtml_Block_Template
{
    public function __construct() {
    		parent::__construct();
    		$this->setTemplate('locator/bulk.phtml');
    		$this->setFormAction(Mage::getUrl('*/*/new'));
    }
}
