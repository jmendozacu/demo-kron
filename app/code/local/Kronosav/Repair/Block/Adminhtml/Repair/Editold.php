<?php
class Kronosav_Repair_Block_Adminhtml_Repair_Editold extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'repair';
		$this->_controller = 'adminhtml_repair';
		$this->updateButton('save','label',Mage::helper('repair')->__('Update Item'));
		$this->removeButton('delete','label',Mage::helper('repair')->__('Delete'));
	}
	public function getHeaderText()
	{
		$registry = Mage::registry('customeredit_data');
		if($registry) {
			return Mage::helper('repair')->__("Edit Repair Log");
			
		} else {
		
			return Mage::helper('repair')->__('Add Repair Details');
		}
	}
}
