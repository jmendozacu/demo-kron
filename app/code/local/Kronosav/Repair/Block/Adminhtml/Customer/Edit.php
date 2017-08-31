<?php
class Kronosav_Repair_Block_Adminhtml_Customer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'repair';
		$this->_controller = 'adminhtml_Customer';
		// $this->updateButton('save','label',Mage::helper('repair')->__('Save'));
		$data = array(
        'label' =>  'Back',
        'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/new') . '\')',
        'class'     =>  'back'
		);
		$this->addButton ('my_back', $data, 0, 100,  'header'); 
		$this->_removeButton('back');
		$this->addButton('reset','label',Mage::helper('repair')->__('Reset'));
		$this->addButton('save','label',Mage::helper('repair')->__('Save'));
		$this->removeButton('delete','label',Mage::helper('repair')->__('Delete'));
		$this->removeButton('back','label',Mage::helper('repair')->__('Back'));
	}
	public function getHeaderText()
	{
		$registry = Mage::registry('customeredit_data');
		if($registry) {
			return Mage::helper('repair')->__("Add Repair Log");
			
		} else {
		
			return Mage::helper('repair')->__('Add Repair Details');
		}
	}
}
