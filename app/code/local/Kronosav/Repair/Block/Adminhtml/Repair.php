<?php
class Kronosav_Repair_Block_Adminhtml_Repair extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_blockGroup = 'repair';
		$this->_controller = 'adminhtml_repair';
		$this->_headerText = Mage::helper('repair')->__('Kronosav Repair');
		$this->_addButtonLabel = Mage::helper('repair')->__('Create New Repair Log');
		parent::__construct();
	}
}